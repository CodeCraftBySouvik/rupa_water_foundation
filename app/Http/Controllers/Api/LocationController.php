<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
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
        $locations = Location::select('location_id', 'title', 'position', 'opening_date')->find($id);

        if(!$locations){
            return response()->json([
                'message' => 'Location not found',
                'success' => false,
                'data'    => null
            ], Response::HTTP_OK);
        }
        return response()->json([
            'message' => 'Location Details Fetched Successfully',
            'success' => true,
            'data'    => $locations
        ], Response::HTTP_OK);
    }


}
