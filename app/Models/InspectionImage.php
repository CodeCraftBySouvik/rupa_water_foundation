<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspectionImage extends Model
{
    //
    protected $table = "inspection_images";
    protected $fillable = [
       'inspection_id',
       'image_path'
    ];

    public function inspection(){
        return $this->belongsTo(Inspection::class,'inspection_id','id');
    }

}
