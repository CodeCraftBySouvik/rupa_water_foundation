<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Inspection;
use App\Models\User;
use App\Models\InspectionImage;
use App\Models\EmployeeZoneAssignment;
use App\Models\ZoneWiseLocation;
use Illuminate\Http\Response;

class LocationController extends Controller
{
    public function location(){
        $location = Location::all();
        return response()->json([
            'message' => 'Location Fetched Successfully',
            'status' => true,
            'data'    => $location
        ],Response::HTTP_OK);
    }

   
    public function details($id)
{
    $location = Location::select('location_id', 'title', 'position', 'opening_date')->find($id);

    if (!$location) {
        return response()->json([
            'message' => 'Location not found',
            'status' => false,
            'data' => null
        ], Response::HTTP_OK);
    }

    //  Always fetch latest inspection for this location, regardless of user
    $inspection = Inspection::where('location_id', $id)
        ->latest('id')
        ->first();

    if ($inspection) {
        $checkedByUser = User::find($inspection->checked_by);
        $images = InspectionImage::where('inspection_id', $inspection->id)
            ->get()
            ->map(fn($img) => asset($img->image_path));
    } else {
        $checkedByUser = null;
        $images = [];
    }

    return response()->json([
        'message' => 'Location Details Fetched Successfully',
        'status' => true,
        'data' => [
            'location_id' => $location->location_id,
            'title' => $location->title,
            'position' => $location->position,
            'opening_date' => $location->opening_date,
            'last_checked_by' => $checkedByUser ? $checkedByUser->name : 'No Data Yet',
            'last_checked_date' => $inspection ? $inspection->checked_date : null,
            'images' => $images,
            'report' => $inspection ? [
                'repairing'    => $inspection->repairing,
                'water_quality' => $inspection->water_quality,
                'electric_available' => $inspection->electric_available,
                'cooling_system' => $inspection->cooling_system,
                'cleanliness' => $inspection->cleanliness,
                'tap_condition' => $inspection->tap_condition,
                'electric_meter_working' => $inspection->electric_meter_working,
                'compressor_condition' => $inspection->compressor_condition,
                'light_availability' => $inspection->light_availability,
                'filter_condition' => $inspection->filter_condition,
                'electric_usage_method' => $inspection->electric_usage_method,
                'notes' => $inspection->notes,
            ] : null,
        ]
    ], Response::HTTP_OK);
}

    public function getUserAssignedZonesLocations(){
        $userId = auth()->id();
        $user = User::find($userId);

        $zoneIds = [];

        if ($user && $user->role === 'supervisor') {
            // Step 1: Get all employees assigned to this supervisor
            $employeeIds = User::where('supervisor_id', $userId)
                ->where('role', 'employee')
                ->pluck('id')
                ->toArray();

            // Step 2: Get zone IDs assigned to these employees
            $zoneIds = EmployeeZoneAssignment::whereIn('employee_id', $employeeIds)
                ->where('status', 'active')
                ->pluck('zone_id')
                ->toArray();

        } else {
            // Normal employee flow
            $zoneIds = EmployeeZoneAssignment::where('employee_id', $userId)
                ->where('status', 'active')
                ->pluck('zone_id')
                ->toArray();
        }

        // Step 3: Get locations based on those zone IDs from zone_wise_locations
        $locations = ZoneWiseLocation::with('zone_name', 'location_details')
            ->whereIn('zone_id', $zoneIds)
            ->where('status', 'active')
            ->get();
        
        // Step 4: Format the result
        $result = $locations->map(function($item){
            return [
                'id'              => $item->id,
                'zone_id'         => $item->zone_id,
                'zone_name'       => $item->zone_name->name ?? 'N/A',
                'location_id'     => $item->location_id,
                'location_title'  => $item->location_details->title ?? 'N/A',
            ];
        });

        return response()->json([
            'message'   => 'Location Fetched Successfully for user:' .$user->name,
            'success'   => true,
            'locations' => $result,
        ]);
    }


}
