<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoneWiseLocation extends Model
{
    protected $table = "zone_wise_locations";
    protected $fillable = [
        'zone_id',
        'location_name',
        'status'
    ];

    public function zone_name(){
        return $this->belongsTo(Zone::class,'zone_id','id');
    }


}
