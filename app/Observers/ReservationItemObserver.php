<?php

namespace App\Observers;
use Carbon\Carbon;
use App\Models\ReservationsItem;

class ReservationItemObserver
{
    public function updating(ReservationsItem $item)
    {
        $this->handleCancellation($item, 'op_one');
        
        if ($item->is_round_trip) {
            $this->handleCancellation($item, 'op_two');
        }
    }
    
    protected function handleCancellation(ReservationsItem $item, $operation)
    {
        $statusField = "{$operation}_status";
        $cancelledAtField = "{$operation}_cancelled_at";
        $levelField = "{$operation}_cancellation_level";
        $pickupField = "{$operation}_pickup";
        
        // Verificar si el estado cambió a CANCELLED
        // Solo actuar si el estado cambió a CANCELLED
        if ($item->isDirty($statusField) && $item->$statusField === 'CANCELLED') {
            // Registrar fecha de cancelación
            // Registrar fecha y hora exacta de la cancelación
            $item->$cancelledAtField = now();
            
            // Determinar nivel de cancelación
            // Determinar si fue cancelación en RESERVA u OPERACIÓN
            $pickupDate = $item->$pickupField ? Carbon::parse($item->$pickupField)->startOfDay() : null;
            $today = Carbon::today();
            
            if ($pickupDate && $pickupDate->equalTo($today)) {
                $item->$levelField = 'OPERATION'; // Cancelación el mismo día del servicio
            } else {
                $item->$levelField = 'RESERVATION'; // Cancelación antes del día del servicio
            }
        }
    }
}
