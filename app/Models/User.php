<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'role', 
        'status', 
        'supervisor_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Always encrypt the password when it is updated.
     *
     * @param $value
    * @return string
    */
    // public function setPasswordAttribute($value)
    // {
    //     $this->attributes['password'] = bcrypt($value);
    // }

    public function getJWTIdentifier()
    {
        return $this->getKey(); // user ID
    }

     public function getJWTCustomClaims()
    {
        return [];
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }


     public function zones() {
        return $this->belongsToMany(
            Zone::class,
            'employee_zone_assignments',
            'employee_id',
            'zone_id'
        )->withPivot('status', 'assigned_date', 'transferred_date');
    }

    public function locations() {
        return $this->belongsToMany(
            ZoneWiseLocation::class,
            'employee_location_assignments',
            'employee_id',
            'location_id'
        )->withPivot('zone_id', 'status', 'assigned_date', 'transferred_date');
    }
}
