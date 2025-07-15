<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AboutUs;
use Illuminate\Http\Response;

class AboutUsController extends Controller
{
    public function show(){
        $about = AboutUs::first();
        return response()->json([
            'message' => 'About Us Data Fetched Successfully',
            'success' => true,
            'data'    => $about
        ],Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $about = AboutUs::findOrFail($id);
        $about->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'About Us updated successfully',
            'data'    => $about->fresh(),
        ], Response::HTTP_OK);
    }
}
