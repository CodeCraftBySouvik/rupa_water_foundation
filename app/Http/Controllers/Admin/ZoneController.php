<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Zone;
use App\Models\ZoneWiseLocation;
use Illuminate\Pagination\Paginator;


class ZoneController extends Controller
{
    
    
    public function index(){
        $zones = Zone::withCount('zoneLocations')->with('zoneLocations')->latest()->paginate(10);
        return view('admin.zone.index',compact('zones'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

       $zone = Zone::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json($zone);
    }

    public function toggleStatus($id){
        $zone = Zone::findOrFail($id);
        $zone->status = ($zone->status == 'Active') ? 'Inactive' : 'Active';
        $zone->save();

        return response()->json([
            'status' => $zone->status
        ]);
    }

    public function edit($id) {
        $zone = Zone::findOrFail($id);
        return response()->json($zone);
   }

    public function update(Request $request)
    {
        $request->validate([
            'zone_id'     => 'required|integer|exists:zones,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $zone = Zone::findOrFail($request->zone_id);

        $zone->name = $request->name;
        $zone->description = $request->description;
        $zone->save();

        $zone->load('zoneLocations'); // Eager-load locations
        $locationCount = $zone->zoneLocations->count();

        return response()->json([
            'status' => true,
            'message' => 'Zone updated successfully!',
            'zone' => $zone,
            'location_count' => $locationCount,
            'locations'       => $zone->zoneLocations->map(function ($loc) {
                return [
                    'location_name' => $loc->location_name,
                    'status'        => $loc->status,
                ];
            }),

        ]);
    }

    // Get Location 
    public function getLocations($id){
        $zone = Zone::with('zoneLocations')->findOrFail($id);
        return response()->json([
            'locations' => $zone->zoneLocations->map(function ($loc) {
                return [
                    'location_name' => $loc->location_name,
                    'status'        => $loc->status,
                ];
            }),
        ]);
    }


    // Zone wise location
    public function zoneWiseLocationIndex(Request $request){
        $getZones = Zone::latest()->get();
         $locations = ZoneWiseLocation::latest()->get();

         $editLocation = null;
         if($request->has('edit')){
            $editLocation = ZoneWiseLocation::findOrFail($request->edit);
         }
        return view('admin.zone.location.index',compact('getZones','locations','editLocation'));
    }

    public function zoneWiseLocationStore(Request $request){
         $request->validate([
            'zone_id'       => 'required|exists:zones,id',
            'location_name' => 'required|string|max:255'
         ]);

         ZoneWiseLocation::create($request->all());
         return redirect()->back()->with('success', 'Zone Location created successfully.');
    }

    public function zoneWiseLocationUpdate(Request $request,$id){
        $request->validate([
            'zone_id'       => 'required|exists:zones,id',
            'location_name' => 'required|string|max:255',
       ]);

       $location = ZoneWiseLocation::findOrFail($id);
       $location->update($request->all());

        return redirect()->route('zone.location.index')->with('success', 'Zone Location updated successfully.');

    }

    public function zoneWiseLocationStatus(Request $request,$id){
        $location = ZoneWiseLocation::findOrFail($id);
        $location->status = $request->status;
        $location->save();

        return response()->json([
            'success' => true,
            'status' => $location->status
        ]);

    }

    public function zoneWiseLocationDelete($id){
        $location = ZoneWiseLocation::findOrFail($id);
        $location->delete();

        return response()->json([
              'success' => true,
              'message' => 'Zone location deleted successfully.'
        ]);
    }


    // Zone Wise Employee
    public function zoneWiseEmployeeIndex(){
        return view('admin.zone.location.employee.index');
    }
}
