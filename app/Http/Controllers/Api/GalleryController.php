<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gallery;

class GalleryController extends Controller
{
     public function index()
    {
        $galleries = Gallery::all()->map(function ($item) {
            $paths = explode(',', $item->image_path);

            return [
                'id'     => $item->id,
                'images' => collect($paths)->map(function ($path) {
                    return url($path); // Full URL to image
                })->values()
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => $galleries
        ]);
    }

    
}
