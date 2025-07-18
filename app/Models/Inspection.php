<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    protected $table = "inspections";
    protected $fillable = [
        'location_id', 
        'checked_by', 
        'checked_date', 
        'water_quality', 
        'electric_available', 
        'cooling_system', 
        'cleanliness', 
        'tap_glass_condition', 
        'electric_meter_working', 
        'compressor_condition', 
        'light_availability', 
        'filter_condition', 
        'electric_usage_method', 
        'notes'
    ];

    public function location(){
        return $this->belongsTo(Location::class,'location_id','id');
    }

    public function checker(){
        return $this->belongsTo(User::class,'checked_by','id');
    }
}
