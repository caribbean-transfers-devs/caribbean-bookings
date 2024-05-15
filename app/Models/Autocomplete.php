<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autocomplete extends Model
{
    use HasFactory;

    protected $table = 'autocomplete';


    public function destination()
    {
        return $this->belongsTo(Zones::class, 'zone_id', 'id');
    }

}
