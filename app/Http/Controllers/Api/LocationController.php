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

    


}
