<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZonesEnterprise extends Model
{
    use HasFactory;

    protected $table = 'zones_enterprises';

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id', 'id');
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id', 'id');
    }
}
