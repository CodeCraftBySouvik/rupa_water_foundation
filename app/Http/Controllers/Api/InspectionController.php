<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Inspection;
use App\Models\InspectionImage;

class InspectionController extends Controller
{
    public function store(Request $request){
         $rules = [
            'location_id'            => 'required|exists:locations,id',
            'checked_by'             => 'required|exists:users,id',
            'checked_date'           => 'required|date',
            'water_quality'          => 'required|in:good,poor',
            'electric_available'     => 'required|in:yes,no',
            'cooling_system'         => 'required|in:working,not working',
            'cleanliness'            => 'required|in:clean,dirty',
            'tap_glass_condition'    => 'required|in:present,not present',
            'electric_meter_working' => 'required|in:yes,no',
            'compressor_condition'   => 'required|in:ok,not ok',
            'light_availability'     => 'required|in:yes,no',
            'filter_condition'       => 'required|in:ok,not ok',
            'electric_usage_method'  => 'required|in:hooking,proper',
            'notes'                  => 'nullable|string|max:1000',
        ];

         $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $inspection = Inspection::create($validator->validated());

        return response()->json([
            'status'     => true,
            'message'    => 'Inspection stored successfully',
            'inspection' => $inspection,
        ], 200);
    }


     public function inspectionGalleryStore($id)
    {
        $inspection = Inspection::find($id);

        if (!$inspection) {
            return response()->json([
                'status'  => false,
                'message' => 'Inspection not found',
            ], 404);
        }

        $images = InspectionImage::where('inspection_id', $id)
                   ->get()
                   ->map(function ($img) {
                       return [
                           'url' => asset($img->image_path), // full URL
                       ];
                   });

        return response()->json([
            'status'       => true,
            'inspectionId' => $id,
            'images'       => $images,
        ]);
    }





}
