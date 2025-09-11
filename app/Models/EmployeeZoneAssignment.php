<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeZoneAssignment extends Model
{
    protected $table = "employee_zone_assignments";
    protected $fillable = [
        'employee_id',  
        'zone_id',
        'status',
        'assigned_date',
        'transferred_date'
    ];
}
