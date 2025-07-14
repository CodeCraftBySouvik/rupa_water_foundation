<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inspection;
use App\Models\Location;

class InspectionController extends Controller
{

    public function rules(): array
    {
        return [
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
    }

    public function index(){
        $inspections = Inspection::all();
        return view('admin.inspection.index',compact('inspections'));
    }

    public function create(){
        $locations = Location::pluck('title','id');
        return view('admin.inspection.create',compact('locations'));
    }

    public function store(Request $request){
        $request->validate($this->rules());
    }



}
