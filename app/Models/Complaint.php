<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $table = "complaints";
    protected $fillable = [
        'user_id',
        'description',
        'images'
    ];

    public function user_details(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
