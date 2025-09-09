<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Zone;

class ZoneController extends Controller
{
    public function index(){
        $zones = Zone::latest()->get();
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

        return response()->json([
            'status' => true,
            'message' => 'Zone updated successfully!',
            'zone' => $zone
        ]);
    }


    // Zone wise location
    public function zoneWiseLocationIndex(){
        $getZones = Zone::latest()->get();
        return view('admin.zone.location.index',compact('getZones'));
    }

    

}
