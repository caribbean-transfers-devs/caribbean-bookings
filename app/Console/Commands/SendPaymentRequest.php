<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Traits\ApiTrait;
use App\Traits\FollowUpTrait;
use App\Traits\LoggerTrait;
use App\Traits\MailjetTrait;
use App\Traits\QueryTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class SendPaymentRequest extends Command
{
    use LoggerTrait, FollowUpTrait, MailjetTrait, QueryTrait, ApiTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-payment-request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía la solicitud de pago por correo para todas las reservaciones pendientes. En una ventana de tiempo definida. Sólo se les enviará a aquellas reservas que no se les haya enviado antes la solicitud de pago';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $process_id = $this->generateRandomProcessId();

        $this->createLog([
            'type' => 'info',
            'process_id' => $process_id,
            'category' => $this->signature,
            'message' => "Iniciando $this->signature",
        ]);
        
        $maxHoursSinceCreation = 48;
        $minHoursBeforeSending = 3;
        $now = Carbon::now();
        $waitingSecondsToSendNextEmail = 10;
        
        $reservations = Reservation::
        // ->where('accept_messages', 1) // Pendiente revisar este campo. Por el momento no es necesario porque no se está utilizando esta columna (no modificar)
        // whereIn('id', [68361,       68359,      68360,      68354,  68349,      68347]) Se puede probar con estos. El resultado de reservations final deben de ser los pendientes
                    // pendiente, confirmado, duplicado, cancelado,   pendiente,    credito
        where('payment_request_sent', 0)
        ->where('is_duplicated', 0)
        ->where('is_quotation', 0)
        ->where('is_cancelled', 0)
        ->where(function ($q) use ($now) {
            $q->whereNull('expires_at')
            ->orWhere('expires_at', '>', $now);
        })
        ->whereBetween('created_at', [
            $now->copy()->subHours($maxHoursSinceCreation),
            $now->copy()->subHours($minHoursBeforeSending),
        ])
        ->get();
        
        $reservation_ids = $reservations->pluck('id')->implode(',');
        $bookings = $this->queryBookings("AND rez.id IN ($reservation_ids)", '', []);

        $reservations = $reservations->filter(function ($reservation) use ($bookings) {
            return collect($bookings)->contains(function ($booking) use ($reservation) {
                return !empty($booking->reservation_id)
                    && $booking->reservation_id == $reservation->id
                    && $booking->reservation_status === 'PENDING';
            });
        })->values();

        $number_of_reservations = sizeof($reservations);
        $this->createLog([
            'type' => 'info',
            'process_id' => $process_id,
            'category' => $this->signature,
            'message' => "Se encontraron: $number_of_reservations reservaciones",
        ]);

        $counter = 0;
        foreach($reservations as $reservation) {
            try {
                $bookings = $this->queryBookings("AND rez.id = $reservation->id", '', []); // Aquí se obtienen los pendientes de nuevo, para volver a validar que sigan pendientes (por el tema del sleep)
                if( sizeof($bookings) === 0 ) continue;
                $booking = $bookings[0];
                if( !isset($booking->reservation_status) || $booking->reservation_status !== 'PENDING') continue;

                try {
                    $this->sendMail($reservation);
                } catch(Exception $e) {
                    $this->createLog([
                        'type' => 'error',
                        'process_id' => $process_id,
                        'category' => $this->signature,
                        'message' => "Error en sendMail. No se pudo enviar el correo.",
                        'exception' => $e,
                    ]);
                    continue;
                }
    
                $this->create_followUps($reservation->id, "El sistema (robot), ha enviado E-mail (solicitúd de pago) para la reservación: $reservation->id", 'INTERN', 'SISTEMA');
                $reservation->payment_request_sent = 1;
                $reservation->save();

                $counter++;
                $this->createLog([
                    'type' => 'info',
                    'process_id' => $process_id,
                    'category' => $this->signature,
                    'message' => "Se envió el correo a la reservación: $reservation->id",
                ]);
            } catch(Exception $e) {
                $this->createLog([
                    'type' => 'error',
                    'process_id' => $process_id,
                    'category' => $this->signature,
                    'exception' => $e,
                ]);
            }

            sleep($waitingSecondsToSendNextEmail);
        }
        
        $this->createLog([
            'type' => 'info',
            'process_id' => $process_id,
            'category' => $this->signature,
            'message' => "Proceso $this->signature terminado. Total de correos enviados: $counter",
        ]);
    }


    private function sendMail(Reservation $reservation) {
        $response = $this->sendPaymentRequestApi($reservation->id, $reservation->language);

        if($response['status'] == false):
            throw new Exception('No se pudo obtener la vista desde el api');
        endif;

        $email_data = array(
            "Messages" => array(
                array(
                    "From" => array(
                        "Email" => 'bookings@caribbean-transfers.com',
                        "Name" => "Bookings"
                    ),
                    "To" => array(
                        array(
                            "Email" => $reservation->client_email,
                            "Name" => $reservation->client_first_name,
                        )
                    ),
                    "Bcc" => array(
                        array(
                            "Email" => 'bookings@caribbean-transfers.com',
                            "Name" => "Bookings"
                        )
                    ),
                    "Subject" => (($reservation->language == "en")?'Payment request':'Solicitúd de pago'),
                    "TextPart" => (($reservation->language == "en")?'Dear client':'Estimado cliente'),
                    "HTMLPart" => $response['data']
                )
            )
        );


        $email_response = $this->sendMailjet($email_data);

        if(!(isset($email_response['Messages'][0]['Status']) && $email_response['Messages'][0]['Status'] == "success")) {
            throw new Exception( json_encode($email_response) );
        }
    }

    private function generateRandomProcessId() {
        $length = 32;
        return bin2hex(random_bytes($length / 2));
    }
}
