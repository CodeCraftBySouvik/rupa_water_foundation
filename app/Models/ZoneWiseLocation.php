<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoneWiseLocation extends Model
{
    protected $table = "zone_wise_locations";
    protected $fillable = [
        'zone_id',
        'location_id',
        'location_number',
        'title',
        'position',
        'opening_date',
        'status'
    ];

    public function zone_name(){
        return $this->belongsTo(Zone::class,'zone_id','id');
    }

    public function location_details(){
        return $this->belongsTo(Location::class,'location_id','id');
    }


}
