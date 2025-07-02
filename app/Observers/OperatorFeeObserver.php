<?php

namespace App\Observers;
use App\Models\OperatorFee;
use App\Models\OperatorFeeLog;

class OperatorFeeObserver
{
    public function created(OperatorFee $operatorFee)
    {
        $this->logAction($operatorFee, 'create', null, $operatorFee->toArray());
    }

    public function updated(OperatorFee $operatorFee)
    {
        $original = $operatorFee->getOriginal();
        $changes = $operatorFee->getChanges();
        
        $oldData = array_intersect_key($original, $changes);
        $newData = $changes;

        $this->logAction($operatorFee, 'update', $oldData, $newData);
    }

    public function deleted(OperatorFee $operatorFee)
    {
        $this->logAction($operatorFee, 'delete', $operatorFee->toArray(), null);
    }

    protected function logAction($operatorFee, $action, $oldData, $newData)
    {
        OperatorFeeLog::create([
            'operator_fee_id' => $operatorFee->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'old_data' => $oldData,
            'new_data' => $newData,
            'notes' => $this->generateNotes($action, $oldData, $newData)
        ]);
    }

    protected function generateNotes($action, $oldData, $newData)
    {
        switch ($action) {
            case 'create':
                return 'Registro creado inicialmente';
            case 'update':
                $notes = [];
                foreach ($newData as $field => $value) {
                    $oldValue = $oldData[$field] ?? null;

                    // Manejo especial para campos que son arrays
                    if ($field === 'zone_ids') {
                        $notes[] = $this->formatZoneIdsChange($oldValue, $value);
                    } else {
                        $notes[] = sprintf(
                            "Campo '%s' cambiado de '%s' a '%s'",
                            $field,
                            $this->formatValue($oldValue),
                            $this->formatValue($value)
                        );
                    }                    
                }
                return implode("\n", $notes);
            case 'delete':
                return 'Registro eliminado del sistema';
            default:
                return 'Acción desconocida';
        }
    }

    /**
     * Formatea valores para el log de cambios
     */
    protected function formatValue($value)
    {
        if (is_array($value)) {
            return json_encode($value);
        }
        
        if (is_null($value)) {
            return 'NULL';
        }
        
        if ($value === '') {
            return '(vacío)';
        }
        
        return $value;
    }

    /**
     * Genera un mensaje descriptivo para cambios en zone_ids
     */
    protected function formatZoneIdsChange($oldValue, $newValue)
    {
        $oldZones = is_array($oldValue) ? $oldValue : json_decode($oldValue, true) ?? [];
        $newZones = is_array($newValue) ? $newValue : json_decode($newValue, true) ?? [];
        
        $added = array_diff($newZones, $oldZones);
        $removed = array_diff($oldZones, $newZones);
        
        $messages = [];
        
        if (!empty($added)) {
            $messages[] = 'Zonas agregadas: ' . implode(', ', $added);
        }
        
        if (!empty($removed)) {
            $messages[] = 'Zonas eliminadas: ' . implode(', ', $removed);
        }
        
        if (empty($messages)) {
            return 'Cambio en zonas asignadas (sin cambios visibles)';
        }
        
        return 'Cambio en zonas asignadas: ' . implode('; ', $messages);
    }    
}
