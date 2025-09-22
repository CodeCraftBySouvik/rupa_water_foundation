<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Inspection;
use App\Models\InspectionImage;

class InspectionController extends Controller
{
    // public function store(Request $request){
    //      $rules = [
    //         'location_id'            => 'required|exists:locations,id',
    //         'checked_by'             => 'required|exists:users,id',
    //         'checked_date'           => 'required|date',
    //         'water_quality'          => 'required|in:good,poor',
    //         'electric_available'     => 'required|in:yes,no',
    //         'cooling_system'         => 'required|in:working,not working',
    //         'cleanliness'            => 'required|in:clean,dirty',
    //         'tap_glass_condition'    => 'required|in:present,not present',
    //         'electric_meter_working' => 'required|in:yes,no',
    //         'compressor_condition'   => 'required|in:ok,not ok',
    //         'light_availability'     => 'required|in:yes,no',
    //         'filter_condition'       => 'required|in:ok,not ok',
    //         'electric_usage_method'  => 'required|in:hooking,proper',
    //         'notes'                  => 'nullable|string|max:1000',
    //     ];

    //      $validator = Validator::make($request->all(), $rules);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Validation errors',
    //             'errors'  => $validator->errors(),
    //         ], 422);
    //     }

    //     $inspection = Inspection::create($validator->validated());

    //     return response()->json([
    //         'status'     => true,
    //         'message'    => 'Inspection stored successfully',
    //         'inspection' => $inspection,
    //     ], 200);
    // }

    public function store(Request $request){
    $rules = [
        'location_id'            => 'required|exists:locations,id',
        'checked_date'           => 'required|date',
        'repairing'              => 'required|in:Floor,Machine',
        'water_quality'          => 'required|in:good,poor',
        'electric_available'     => 'required|in:yes,no',
        'cooling_system'         => 'required|in:working,not working',
        'cleanliness'            => 'required|in:clean,dirty',
        'tap_condition'    => 'required|in:present,not present',
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

    // ✅ Use only validated fields and add current user's ID
    $data = $validator->validated();
    $data['checked_by'] = auth()->id(); // Force checked_by to logged-in user

    $inspection = Inspection::create($data);

    return response()->json([
        'status'     => true,
        'message'    => 'Inspection stored successfully',
        'inspection' => $inspection,
    ], 200);
}



    //  public function inspectionGalleryStore($id)
    // {
    //     $inspection = Inspection::find($id);

    //     if (!$inspection) {
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'Inspection not found',
    //         ], 404);
    //     }

    //     $images = InspectionImage::where('inspection_id', $id)
    //               ->get()
    //               ->map(function ($img) {
    //                   return [
    //                       'url' => asset($img->image_path), // full URL
    //                   ];
    //               });

    //     return response()->json([
    //         'status'       => true,
    //         'inspectionId' => $id,
    //         'images'       => $images,
    //     ]);
    // }
    
   public function inspectionGalleryStore(Request $request)
    {
        $request->validate([
            'inspection_id' => 'required|exists:inspections,id',
            'images'        => 'required|array|min:1',
            'images.*'      => 'image|max:5120',
        ]);
    
        $uploadedImages = [];
    
        foreach ($request->file('images') as $img) {
            $name = time() . rand(1000, 9999) . '.' . $img->extension();
            $img->move(public_path('uploads/inspection_galleries'), $name);
    
            $path = 'uploads/inspection_galleries/' . $name;
    
            // Save to DB
            InspectionImage::create([
                'inspection_id' => $request->inspection_id,
                'image_path'    => $path,
            ]);
    
            // Collect full URL
            $uploadedImages[] = asset($path);
        }
    
        return response()->json([
            'status'  => true,
            'message' => 'Images uploaded',
            'images'  => $uploadedImages,
        ]);
    }


      public function inspectionStatus($location_id, $checked_by, $checked_date)
    {
        // cast into array so we can reuse the same validator rule set
        $data = compact('location_id', 'checked_by', 'checked_date');

        validator($data, [
            'location_id'  => 'required|exists:locations,id',
            'checked_by'   => 'required|exists:users,id',
            'checked_date' => 'required|date',
        ])->validate();

        $exists = Inspection::where('location_id',  $location_id)
            ->where('checked_by',   $checked_by)
            ->whereDate('checked_date', $checked_date)
            ->exists();

        return response()->json([
            'status'    => true,
            'submitted' => $exists,
            'message'   => $exists ? 'Already submitted' : 'Not submitted yet',
        ]);
    }






}
