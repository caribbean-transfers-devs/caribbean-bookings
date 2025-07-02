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
                    $notes[] = "Campo '{$field}' cambiado de '{$oldValue}' a '{$value}'";
                }
                return implode("\n", $notes);
            case 'delete':
                return 'Registro eliminado del sistema';
            default:
                return 'Acción desconocida';
        }
    }
}
