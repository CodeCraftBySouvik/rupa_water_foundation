<?php

namespace App\Models; 

use Illuminate\Database\Eloquent\Model;

class EmployeeLocationAssignment extends Model
{
    protected $table = "employee_location_assignments";
    protected $fillable = [
        'employee_id',
        'zone_id',
        'location_id',
        'status',
        'assigned_date',
        'transferred_date'
    ];

    public function locationDetails()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function zone(){
         return $this->belongsTo(Location::class, 'zone_id');
    }
}
