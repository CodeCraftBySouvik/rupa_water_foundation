<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $table = "galleries";
    protected $fillable = ['image_path'];
    public function getImagesAttribute(): array
    {
        return $this->image_path
            ? explode(',', $this->image_path)
            : [];
    }
}
