<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function gallery(){
        // $gallery()
        $galleries = Gallery::all()->map(function($items){
            
        });
    }
}
