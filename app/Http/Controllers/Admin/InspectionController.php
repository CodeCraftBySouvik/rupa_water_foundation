<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Inspection;
use App\Models\{Location, User, InspectionImage};
use Symfony\Component\HttpFoundation\StreamedResponse;


class InspectionController extends Controller
{

    public function rules(): array
    {
        return [
            'location_id'            => 'required|exists:locations,id',
            'checked_by'             => 'required|exists:users,id',
            'checked_date'           => 'required|date',
            'water_quality'          => 'required|in:good,poor',
            'repairing'              => 'required|in:Floor,Machine',
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
            'images.*' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

        public function messages(): array
    {
        return [
            'location_id.required' => 'Please select a location.',
            'checked_by.required' => 'Please select the user who checked.',
            'checked_date.required' => 'Please enter the inspection date.',
            'water_quality.required' => 'Please select the water quality.',
            'repairing.required' => 'Please select the repairing.',
            'electric_available.required' => 'Please specify if electricity is available.',
            'cooling_system.required' => 'Please specify the cooling system condition.',
            'cleanliness.required' => 'Please specify cleanliness status.',
            'tap_condition.required' => 'Please specify tap condition.',
            'electric_meter_working.required' => 'Please specify electric meter condition.',
            'compressor_condition.required' => 'Please specify compressor condition.',
            'light_availability.required' => 'Please specify if light is available.',
            'filter_condition.required' => 'Please specify filter condition.',
            'electric_usage_method.required' => 'Please specify electric usage method.',

            // 🖼️ Custom messages for images
            'images.*.required' => 'Image field is required.',
        ];
    }


     public function index(Request $request)
    {
        // Eager load relationships used in the view for better performance
        $query = Inspection::with(['location', 'checker'])->orderBy('checked_date','desc'); 

        if ($request->filled('start_date') && $request->filled('end_date')) {
            // Parse the start date and set its time to the beginning of the day (00:00:00)
            $start = Carbon::parse($request->start_date)->startOfDay();
            
            // Parse the end date and set its time to the end of the day (23:59:59)
            $end = Carbon::parse($request->end_date)->endOfDay(); 
            
            // Assuming checkedBetweenDates uses whereBetween or equivalent
            $query->checkedBetweenDates($start, $end);
        }

         // ✅ Search filter
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('location', function ($q2) use ($search) {
                    $q2->where('title', 'like', "%{$search}%");
                })
                ->orWhereHas('checker', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                })
                ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $inspections = $query->get();
        return view('admin.inspection.index', compact('inspections'));
    }


    public function create(){
        $locations = Location::pluck('title','id');
        $currentuserId = auth()->id();
        $checkUser = User::where('id', '!=', $currentuserId)->pluck('name', 'id');
        return view('admin.inspection.create',compact('locations', 'checkUser'));
    }

    public function store(Request $request){
    //    dd($request->all());
        $validated = $request->validate($this->rules(),$this->messages());
        $validated['created_by'] = auth()->id();

       $inspection = Inspection::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image->isValid()) {
                    $fileName = time() . rand(10000, 99999) . '.' . $image->extension();
                    $filePath = 'uploads/inspection_galleries/' . $fileName;

                    $image->move(public_path('uploads/inspection_galleries/'), $fileName);
                   
                    InspectionImage::create([
                        'inspection_id' => $inspection->id,
                        'image_path' => $filePath,
                    ]);
                }
            }
        }

        return redirect()->route('inspection.index')->with('success', 'Inspection saved successfully!');
    }

    public function edit($id)
    {
        $inspection = Inspection::findOrFail($id);
        $locations = Location::pluck('title', 'id'); // Adjust if needed
        $currentuserId = auth()->id();
        $checkUser = User::where('id', '!=', $currentuserId)->pluck('name', 'id');      // Adjust if needed
    //    dd($inspection);

        return view('admin.inspection.edit', compact('inspection', 'locations', 'checkUser'));
    }

    public function update(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'id' => 'required|exists:inspections,id',
            'checked_by' => 'required|exists:users,id',
            'location_id' => 'required|exists:locations,id',
            'checked_date' => 'required|date',
            'new_images.*' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value instanceof \Illuminate\Http\UploadedFile) {
                        $ext = strtolower($value->getClientOriginalExtension());
                        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                            $fail('Each image must be a JPG, JPEG, or PNG file.');
                        }
                        if ($value->getSize() > 2048 * 1024) {
                            $fail('Each image must not exceed 2MB.');
                        }
                    }
                },
            ],
        ]);

        $inspection = Inspection::findOrFail($request->id);
        $inspection->update($request->except(['_token']));
         // Handle new image uploads
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $fileName = time() . rand(10000, 99999) . '.' . $image->extension();
                $filePath = 'uploads/inspection_galleries/' . $fileName;
                $image->move(public_path('uploads/inspection_galleries/'), $fileName);

                InspectionImage::create([
                    'inspection_id' => $inspection->id,
                    'image_path' => $filePath,
                ]);
            }
        }


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

    public function export(Request $request)
{
    $request->validate([
        'start_date' => 'nullable|date',
        'end_date'   => 'nullable|date|after_or_equal:start_date',
    ]);

    $query = Inspection::with(['location','checker'])
                         ->orderBy('checked_date', 'desc'); ;

    if ($request->start_date) {
        $query->whereDate('checked_date', '>=', $request->start_date);
    }
    if ($request->end_date) {
        $query->whereDate('checked_date', '<=', $request->end_date);
    }

     // Search filter
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->whereHas('location', fn($q2) => $q2->where('title', 'like', "%{$search}%"))
              ->orWhereHas('checker', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
              ->orWhere('notes', 'like', "%{$search}%");
        });
    }

    $inspections = $query->get();

    $response = new StreamedResponse(function() use ($inspections) {
        $handle = fopen('php://output', 'w');
         // Write UTF-8 BOM
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        // Add CSV header
        fputcsv($handle, [
            'Checked Date',
            'Location',
            'Latitude',
            'Longitude',
            'Address',
            'Checked By',
            'Water Quality',
            'Electric Available',
            'Cooling System',
            'Cleanliness',
            'Tap Condition',
            'Electric Meter',
            'Compressor',
            'Light',
            'Filter',
            'Electric Usage',
            'Notes'
        ]);

        // Add rows
        foreach ($inspections as $in) {
            fputcsv($handle, [
                $in->checked_date,
                $in->location->title ?? '-',
                $in->latitude,
                $in->longitude,
                $in->address,
                $in->checker->name ?? '-',
                $in->water_quality,
                $in->electric_available,
                $in->cooling_system,
                $in->cleanliness,
                $in->tap_condition,
                $in->electric_meter_working,
                $in->compressor_condition,
                $in->light_availability,
                $in->filter_condition,
                $in->electric_usage_method,
                $in->notes,
            ]);
        }

        fclose($handle);
    });

    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition', 'attachment; filename="inspections.csv"');

    return $response;
}




}
