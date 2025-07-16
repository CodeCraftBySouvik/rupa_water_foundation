<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Location;

class LocationController extends Controller
{
    public function location(){
        $locationData = Location::orderBy('location_id', 'asc')->get();
        return view('admin.location.index',compact('locationData'));
    }

    public function location_create_form(){
        return view('admin.location.create');
    }

    public function location_store(Request $request){
        $data = $request->validate([
            'location_id'   => ['required', 'string', 'max:50', Rule::unique('locations', 'location_id')],
            'title'         => 'required|string|max:255',
            'address'       => 'required|string|max:500',

            // nullable but must be 'roadside' or 'complex' if supplied
            'location_type' => ['nullable', Rule::in(['roadside', 'complex'])],

            // nullable, numeric, within valid lat/lng ranges
            'latitude'      => 'nullable|numeric|between:-90,90',
            'longitude'     => 'nullable|numeric|between:-180,180',

            // required enum
            'position'      => ['required', Rule::in(['small', 'big'])],

            // required date (any format accepted by PHP strtotime)
            'opening_date'  => 'required|date',
        ]);

        Location::create($data);
        return redirect()->route('location.index')->with('success','Location created successfully.');
    }

    public function location_edit_form($id){
        
        $locationEdit = Location::findOrFail($id);
        return view('admin.location.edit',compact('locationEdit'));
    }

    public function location_update(Request $request){
        $request->validate([
            'location_id'   => ['required', 'string', 'max:50', Rule::unique('locations', 'location_id')->ignore($request->id)],
            'title'         => 'required|string|max:255',
            'address'       => 'required|string|max:500',
            'location_type' => ['nullable', Rule::in(['roadside', 'complex'])],
            'latitude'      => 'nullable|between:-90,90',
            'longitude'     => 'nullable|between:-180,180',
            'position'      => ['required', Rule::in(['small', 'big'])],
            'opening_date'  => 'required|date',
        ]);

        $locationUpdate = Location::findOrFail($request->id);
         $locationUpdate->location_id = $request->location_id;
         $locationUpdate->title = $request->title;
         $locationUpdate->address = $request->address;
         $locationUpdate->location_type = $request->location_type;
         $locationUpdate->latitude = $request->latitude;
         $locationUpdate->longitude = $request->longitude;
         $locationUpdate->position = $request->position;
         $locationUpdate->opening_date = $request->opening_date;
        $locationUpdate->save();
        return redirect()->route('location.index')->with('success','Location updated successfully.');

    }

    
    

}
