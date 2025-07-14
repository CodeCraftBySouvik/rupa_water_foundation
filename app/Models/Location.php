<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = "locations";
    protected $fillable = [
       'location_id', 
       'title', 
       'address', 
       'location_type',
        'latitude',
         'longitude', 
         'position', 
         'opening_date'
    ];
}
