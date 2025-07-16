<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gallery;

class GalleryController extends Controller
{
      public function index()
    {
        $gallery = Gallery::first(); // Always using the first (or only) row

        if (!$gallery) {
            return response()->json([
                'status' => false,
                'message' => 'No gallery found',
                'images' => [],
            ]);
        }

        $images = collect(explode(',', $gallery->image_path))
                    ->filter()
                    ->map(function ($imgPath) {
                        return asset($imgPath); // full URL to image
                    })
                    ->values();

        return response()->json([
            'status' => true,
            'message' => 'Gallery images fetched successfully',
            'images' => $images,
        ]);
    }


}
