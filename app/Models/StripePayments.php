<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StripePayments extends Model
{
    use HasFactory;

    protected $table = 'stripe_payments';

    protected $fillable = [
        'id',
        'code',
    ];

    protected $casts = [
        'object' => 'array', // Se asegura de que Laravel lo maneje como un array automáticamente
    ];
    
    /**
     * Mutator: Convierte el valor a JSON antes de guardarlo en la base de datos.
     */
    public function setObjectAttribute($value)
    {
        $this->attributes['object'] = json_encode($value ?? []);
    }

    /**
     * Accessor: Decodifica el JSON cuando se obtiene el valor del modelo.
     */
    public function getObjectAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }    
}
