<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ZoneWiseLocation;

class Zone extends Model
{
    protected $table = "zones";
    protected $fillable = [
        'name',
        'description',
        'status'
    ];

    public function zoneLocations(){
        return $this->hasMany(ZoneWiseLocation::class,'zone_id');
    }
}
