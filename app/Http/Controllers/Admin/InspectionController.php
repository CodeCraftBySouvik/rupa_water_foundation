<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Inspection;
use App\Models\{Location, User, InspectionImage};

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
            'tap_condition'          => 'required|in:present,not present',
            'electric_meter_working' => 'required|in:yes,no',
            'compressor_condition'   => 'required|in:ok,not ok',
            'light_availability'     => 'required|in:yes,no',
            'filter_condition'       => 'required|in:ok,not ok',
            'electric_usage_method'  => 'required|in:hooking,proper',
            'notes'                  => 'nullable|string|max:1000',
        ];
    }

    public function index(){
        $inspections = Inspection::latest()->get();
        return view('admin.inspection.index',compact('inspections'));
    }

    public function create(){
        $locations = Location::pluck('title','id');
        $currentuserId = auth()->id();
        $checkUser = User::where('id', '!=', $currentuserId)->pluck('name', 'id');
        return view('admin.inspection.create',compact('locations', 'checkUser'));
    }

    public function store(Request $request){
       // dd($request->all());
        $validated = $request->validate($this->rules());
        Inspection::create($validated);

        return redirect()->route('inspection.index')->with('success', 'Inspection saved successfully!');
    }

    public function edit($id)
    {
        $inspection = Inspection::findOrFail($id);
        $locations = Location::pluck('title', 'id'); // Adjust if needed
        $currentuserId = auth()->id();
        $checkUser = User::where('id', '!=', $currentuserId)->pluck('name', 'id');      // Adjust if needed
       // dd($inspection);

        return view('admin.inspection.edit', compact('inspection', 'locations', 'checkUser'));
    }

    public function update(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'id' => 'required|exists:inspections,id',
            'checked_by' => 'required|exists:users,id',
            'location_id' => 'required|exists:locations,id',
            'checked_date' => 'required|date',
            
        ]);

        $inspection = Inspection::findOrFail($request->id);
        $inspection->update($request->except(['_token']));

        return redirect()->route('inspection.index')->with('success', 'Inspection updated successfully.');
    }

    public function galleryIndex(Request $request, $inspection_id) {
        $inspection = Inspection::findOrFail($inspection_id);
        $gallery   = InspectionImage::where('inspection_id', $inspection_id)->paginate(25);
        return view('admin.inspection.galleryindex', compact('inspection', 'gallery'));   
    }

    public function galleryStore(Request $request)
    {
        $request->validate([
            'inspection_id' => 'required|exists:inspections,id',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',// Multiple images
        ]);

        $inspectionId = $request->input('inspection_id');
        
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $fileName = time() . rand(10000, 99999) . '.' . $image->extension();
                    $filePath = 'uploads/inspection_galleries/' . $fileName;

                    $image->move(public_path('uploads/inspection_galleries/'), $fileName);

                    InspectionImage::create([
                        'inspection_id' => $inspectionId,
                        'image_path' => $filePath,
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Images uploaded successfully.');
    }

    public function galleryEdit($id) {
        $editableGallery = InspectionImage::findOrFail($id);
        $inspection = Inspection::find($editableGallery->inspection_id);
        $gallery = InspectionImage::where('inspection_id', $inspection->id)->paginate(25);

        return view('admin.inspection.galleryindex', compact('editableGallery', 'inspection', 'gallery'));
    }

    public function galleryUpdate(Request $request)
    {
        $request->validate([
            'gallery_id' => 'required|exists:inspection_images,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
        ], [
            'image.image' => 'Uploaded file must be an image.',
            'image.mimes' => 'Image must be a type of: jpg, jpeg, png, webp, gif, or svg.',
            'image.max' => 'Image must not be larger than 5MB.',
            'gallery_id.required' => 'Gallery ID is required.',
            'gallery_id.exists' => 'The selected gallery image does not exist.',
        ]);

        $gallery = InspectionImage::findOrFail($request->gallery_id);

        // Check if image is uploaded
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Delete old image if exists
            if (!empty($gallery->image_path) && file_exists(public_path($gallery->image_path))) {
                unlink(public_path($gallery->image_path));
            }

            // Save new image
            $image = $request->file('image');
            $fileName = time() . rand(10000, 99999) . '.' . $image->extension();
            $filePath = 'uploads/inspection_galleries/' . $fileName;
            $image->move(public_path('uploads/inspection_galleries/'), $fileName);

            // Update only if new image uploaded
            $gallery->update([
                'image_path' => $filePath,
            ]);
        }

        //  No update called if image is not uploaded = old image remains untouched

        return redirect()->route('inspection.galleries.list', ['inspection_id' => $gallery->inspection_id])
                        ->with('success', 'Image updated successfully.');
    }




}
