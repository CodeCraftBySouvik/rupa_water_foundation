<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLocationAssignments extends Model
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
}
