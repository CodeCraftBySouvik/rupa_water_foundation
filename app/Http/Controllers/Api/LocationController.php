<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Inspection;
use App\Models\User;
use App\Models\InspectionImage;
use Illuminate\Http\Response;

class LocationController extends Controller
{
    public function location(){
        $location = Location::all();
        return response()->json([
            'message' => 'Location Fetched Successfully',
            'success' => true,
            'data'    => $location
        ],Response::HTTP_OK);
    }

    public function details($id){
        $location = Location::select('location_id', 'title', 'position', 'opening_date')->find($id);

        if(!$location){
            return response()->json([
                'message' => 'Location not found',
                'success' => false,
                'data'    => null
            ], Response::HTTP_OK);
        }

         // Fetch latest inspection for this location
         $inspection = Inspection::where('location_id', $id)
                    ->latest('checked_date')
                    ->first();

        // If inspection exists, get checked_by name and image URLs
            if ($inspection) {
            $checkedByUser = User::find($inspection->checked_by);
            $images = InspectionImage::where('inspection_id', $inspection->id)
                        ->get()
                        ->map(function ($img) {
                            return asset($img->image_path);  // Return full URL
                        });
        } else {
            $checkedByUser = null;
            $images = [];
        }


        return response()->json([
            'message' => 'Location Details Fetched Successfully',
            'success' => true,
            'data'    => [
                        'location_id'   => $location->location_id,
                        'title'         => $location->title,
                        'position'      => $location->position,
                        'opening_date'  => $location->opening_date,
                        'last_checked_by' => $checkedByUser ? $checkedByUser->name : null,
                        'last_checked_date' => $inspection ? $inspection->checked_date : null,
                        'images'        => $images,
                        'report'             => $inspection ? [
                            'water_quality'          => $inspection->water_quality,
                            'electric_available'     => $inspection->electric_available,
                            'cooling_system'         => $inspection->cooling_system,
                            'cleanliness'            => $inspection->cleanliness,
                            'tap_glass_condition'    => $inspection->tap_glass_condition,
                            'electric_meter_working' => $inspection->electric_meter_working,
                            'compressor_condition'   => $inspection->compressor_condition,
                            'light_availability'     => $inspection->light_availability,
                            'filter_condition'       => $inspection->filter_condition,
                            'electric_usage_method'  => $inspection->electric_usage_method,
                            'notes'                  => $inspection->notes,
                        ] : null,
                    ]
        ], Response::HTTP_OK);
    }


}
