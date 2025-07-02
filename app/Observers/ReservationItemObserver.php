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
        if ($item->isDirty($statusField) && $item->$statusField === 'CANCELLED') {
            // Registrar fecha de cancelación
            $item->$cancelledAtField = now();
            
            // Determinar nivel de cancelación
            $pickupDate = $item->$pickupField ? Carbon::parse($item->$pickupField)->startOfDay() : null;
            $today = Carbon::today();
            
            if ($pickupDate && $pickupDate->equalTo($today)) {
                $item->$levelField = 'OPERATION';
            } else {
                $item->$levelField = 'RESERVATION';
            }
        }
    }
}
