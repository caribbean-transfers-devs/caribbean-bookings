<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\ReservationsItem;

class BackfillCancellationData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cancellations:backfill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rellena datos de cancelación para registros antiguos.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $items = ReservationsItem::where('op_one_status', 'CANCELLED')
            ->orWhere('op_two_status', 'CANCELLED')
            ->get();

        // Definir el rango de fechas (puedes parametrizarlo según necesites)
        // $startDate = now()->subDays(30); // Últimos 30 días por ejemplo
        // $endDate = now();

        // $startDate = "2025-01-01 00:00:00"; // Últimos 30 días por ejemplo
        // $endDate   = "2025-07-02 23:59:59";

        // $items = ReservationsItem::where('op_one_status', 'CANCELLED')
        //         ->orWhere('op_two_status', 'CANCELLED')
        //         ->whereHas('reservations', function($q) use ($startDate, $endDate) {
        //             $q->whereBetween('created_at', [$startDate, $endDate]);
        //         })
        //         ->get();

        $items->each(function ($item) {
            if ($item->op_one_status === 'CANCELLED') {
                $this->processCancellation($item, 'op_one');
            }

            if ($item->is_round_trip && $item->op_two_status === 'CANCELLED') {
                $this->processCancellation($item, 'op_two');
            }

            $item->save();
        });

        $this->info("Procesados {$items->count()} registros.");
    }

    protected function processCancellation($item, $operationPrefix)
    {
        $cancelledAtField = "{$operationPrefix}_cancelled_at";
        $cancellationLevelField = "{$operationPrefix}_cancellation_level";
        $pickupField = "{$operationPrefix}_pickup";
        $updatedAt = $item->updated_at; // Usamos updated_at como aproximación

        $item->$cancelledAtField = $updatedAt;

        $pickupDate = $item->$pickupField ? Carbon::parse($item->$pickupField)->startOfDay() : null;
        $cancellationDate = $updatedAt->startOfDay();

        if ($pickupDate && $pickupDate->equalTo($cancellationDate)) {
            $item->$cancellationLevelField = 'OPERATION';
        } else {
            $item->$cancellationLevelField = 'RESERVATION';
        }
    }    
}
