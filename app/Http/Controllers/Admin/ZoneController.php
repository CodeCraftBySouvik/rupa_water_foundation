<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Zone;
use App\Models\ZoneWiseLocation;
use Illuminate\Pagination\Paginator;
use App\Models\User;
use App\Models\EmployeeZoneAssignment;
use App\Models\EmployeeLocationAssignment;
use App\Models\Location;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ZoneController extends Controller
{
    
    
    public function index(){
        $zones = Zone::withCount(['zoneLocations','zoneEmployees'])->with(['zoneLocations','zoneEmployees'])->latest()->paginate(10);
        return view('admin.zone.index',compact('zones'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|unique:zones,name|string|max:255',
            'description' => 'nullable|string',
        ]);

       $zone = Zone::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);
        // reload zone with counts
        $zone = Zone::with('zoneLocations','zoneEmployees')->find($zone->id);
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

         $zone->load(['zoneLocations.location_details', 'zoneEmployees.employee']);  // Eager-load locations
        $locationCount = $zone->zoneLocations->count();
         $employeeCount = $zone->zoneEmployees->count();
        return response()->json([
            'status' => true,
            'message' => 'Zone updated successfully!',
            'zone' => $zone,
            'location_count' => $locationCount,
            'employee_count' => $employeeCount,
            'locations'       => $zone->zoneLocations->map(function ($loc) {
                return [
                    'location_name' => $loc->location_name,
                    'status'        => $loc->status,
                ];
            }),
            'employees' => $zone->zoneEmployees->map(function ($emp) {
                return [
                    'id'     => $emp->id,
                    'name'   => $emp->employee->name ?? null,
                    'status' => $emp->status,
                ];
            }),

        ]);
    }

    // Get Location 
    public function getLocations($id){
        $zone = Zone::with(['zoneLocations.location_details','zoneEmployees.employee'])->findOrFail($id);
        return response()->json([
            'locations' => $zone->zoneLocations->map(function ($loc) {
                return [
                    'id'            => $loc->id,      
                    'location_name' => $loc->location_details->title ?? null,
                    'status'        => $loc->status,
                ];
            }),
            'employees' => $zone->zoneEmployees->map(function ($emp) {
                return [
                    'id'            => $emp->id,      
                    'employee_name' => $emp->employee->name ?? null,
                    'status'        => $emp->status,
                ];
            }),
        ]);
    }
    


    // Zone wise location
    public function downloadSampleCsv(){
         $columns = ['Zone', 'Location', 'Opening Date', 'Size'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');

            // Add header row
            fputcsv($file, $columns);

            // Static data
            $staticData = [
                ['North Zone', 'Thakurpukur Police Station', '25.12.12', 'Small'],
                ['North Zone', 'Behala Police Station', '26.12.12', 'Big'],
            ];

            foreach ($staticData as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="zone_location_export.csv"',
        ]);
    }

   

    public function import(Request $request)
    {
    
        $errors = [];
        $inserted = 0;

        try {
            $file = $request->file('file');

            if (!$file || !$file->isValid()) {
                throw new \Exception('Invalid file upload.');
            }

            $path = $file->getRealPath();
            $fileHandle = fopen($path, 'r');

            $header = fgetcsv($fileHandle); // Skip header row

            while ($row = fgetcsv($fileHandle)) {
                if (count($row) < 4) {
                    $errors[] = "Invalid row format: " . implode(',', $row);
                    continue;
                }

                $zoneName      = trim($row[0]); 
                $locationTitle = trim($row[1]); 
                $openingDate   = trim($row[2]); 
                $size          = trim($row[3]); 

                try {
                    // Find Zone
                    $zone = Zone::where('name', $zoneName)->first();
                    if (!$zone) {
                        $errors[] = "Zone '{$zoneName}' not found.";
                        continue;
                    }

                    // Generate new Location always (even if duplicate)
                    $lastId = Location::max('id') ?? 0;
                    $nextNumber = $lastId + 1;
                    $newLocationId = 'RUPA' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                    $location = Location::create([
                        'location_id'   => $newLocationId,
                        'title'         => $locationTitle,
                        'position'      => $size,
                        'opening_date'  => $openingDate,
                        'status'        => 'active',
                    ]);

                    // Parse date safely
                    $parsedDate = \DateTime::createFromFormat('d.m.y', $openingDate);
                    if (!$parsedDate) {
                        $errors[] = "Invalid date format: {$zoneName}, {$openingDate}";
                        continue;
                    }

                    // Always insert ZoneWiseLocation (duplicates allowed)
                    ZoneWiseLocation::create([
                        'zone_id'       => $zone->id,
                        'location_id'   => $location->id,
                        'position'      => $size,
                        'opening_date'  => $parsedDate->format('Y-m-d'),
                        'status'        => 'active',
                    ]);

                    $inserted++;

                } catch (\Exception $ex) {
                    $errors[] = "Row failed: " . implode(',', $row) . " | Error: " . $ex->getMessage();
                    continue;
                }
            }

            fclose($fileHandle);

            return redirect()->back()->with([
                'success'  => "CSV imported successfully. Inserted: {$inserted}, Errors: " . count($errors),
                'import_errors'   => $errors
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function zoneWiseLocationIndex(Request $request){
        $getZones = Zone::latest()->get();
        $getLocations = Location::all();
        $query  = ZoneWiseLocation::with('location_details')->latest();
        
         if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('location_details', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            })
            ->orWhereHas('zone_name', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $locations = $query->get();

        return view('admin.zone.location.index',compact('getZones','getLocations','locations'));
    }

    public function zoneWiseLocationStore(Request $request){
        $validated = $request->validate([
            'zone_id'       => 'required|exists:zones,id',
            'location_id' => 'required|string|max:255'
         ]);
           // Check if this zone-location combination already exists
                $exists = ZoneWiseLocation::where('zone_id', $validated['zone_id'])
                            ->where('location_id', $validated['location_id'])
                            ->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'This Zone and Location combination already exists.');
        }
         ZoneWiseLocation::create($validated);
         return redirect()->back()->with('success', 'Zone Location created successfully.');
    }

    public function zoneWiseLocationEdit($id){
        $location = ZoneWiseLocation::with('location_details')->findOrFail($id);
        return response()->json([
             'id' => $location->id,
            'zone_id' => $location->zone_id,
            'location_id' => $location->location_id,
            'location_name' => $location->location_details->title, 
            'position' => $location->position,
            'opening_date' => $location->opening_date,
            'status' => $location->status,
        ]);
    }

   

     public function zoneWiseLocationUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'zone_id'     => 'required|exists:zones,id',
            'location_id' => 'required|string|max:255', // this is the title/name of the location
            'position'    => 'nullable|string|max:255', // optional: size/position
            'opening_date'=> 'nullable|date_format:d.m.y' // optional
        ]);

        $locationEntry = ZoneWiseLocation::findOrFail($id);

        // Check for duplicates
        $exists = ZoneWiseLocation::where('zone_id', $validated['zone_id'])
            ->where('location_id', $locationEntry->location_id) // use location_id from ZoneWiseLocation
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This Zone and Location combination already exists.'
            ], 422);
        }

        // Update Location table
        $location = Location::find($locationEntry->location_id);
        if ($location) {
            $location->update([
                'title'        => $validated['location_id'], // update title
                'position'     => $validated['position'] ?? $location->position,
                'opening_date' => isset($validated['opening_date'])
                                    ? \DateTime::createFromFormat('d.m.y', $validated['opening_date'])->format('Y-m-d')
                                    : $location->opening_date,
            ]);
        }

        // Update ZoneWiseLocation table
        $locationEntry->update([
            'zone_id'      => $validated['zone_id'],
            'position'     => $validated['position'] ?? $locationEntry->position,
            'opening_date' => isset($validated['opening_date'])
                                ? \DateTime::createFromFormat('d.m.y', $validated['opening_date'])->format('Y-m-d')
                                : $locationEntry->opening_date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Zone Location and associated Location updated successfully.'
        ]);
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
        $supervisors = User::where([
            'role' => 'supervisor',
            'status' => 'active'
        ])->get();

        $zones = Zone::where('status', 'Active')
        ->with(['zoneLocations' => function ($query) {
            $query->where('status', 'Active');
        }])->get();

         // Get all employees (users who are not HO)
        $userlist = User::with([
            'supervisor:id,name,email',
            'zones:id,name',        // multiple zones if assigned
            'locations.location_details',
            'complaints'
        ])
        ->select('id', 'name', 'mobile', 'email', 'password', 'role', 'status', 'supervisor_id')
        ->where('name', '!=', 'Super Admin') 
        ->where('role', '!=', 'ho') 
        ->get();

        return view('admin.zone.location.employee.index',compact('supervisors','zones','userlist'));
    }

    public function zoneWiseEmployeeStore(Request $request){
        // dd($request->all());
       $validated = $request->validate([
                        'name'         => 'required|string|max:255',
                        'email'        => 'required|email|unique:users,email',
                        'password'     => 'required|min:6',
                        'phone'        => 'required|digits:10',
                        'alternate_number'        => 'required|digits:10',
                        'role'         => 'required|in:ho,supervisor,employee,complaint',
                        // 'supervisor_id'=> 'required|exists:users,id',
                        'supervisor_id' => [
                            Rule::requiredIf($request->role === 'employee'),
                            'nullable',
                            'exists:users,id',
                        ],
                        'zone_id'       => 'required|array',
                        'zone_id.*'     => 'exists:zones,id',
                    ]);

        \DB::beginTransaction();
        try {
            // Create Employee (assuming your User model holds employees too)
            $employee = User::create([
                'name'         => $validated['name'],
                'email'        => $validated['email'],
                'password'     => Hash::make($validated['password']),
                'mobile'        => $validated['phone'],
                'alternate_number'        => $validated['alternate_number'] ?? null,
                'role'         => $validated['role'],
                'supervisor_id'=> $validated['supervisor_id'] ?? null,
                'status'       => 'active',
            ]);

           foreach ($validated['zone_id'] as $zoneId){
            // Assign Zone
            EmployeeZoneAssignment::create([
                'employee_id'   => $employee->id,
                'zone_id'       => $zoneId,
                'status'       => 'active',
                'assigned_date'=> now()->toDateString(),
            ]);
        }

            \DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => 'Employee created successfully.',
                'employee' => $employee, // you can customize this payload
            ]);

        } catch (\Exception $e) {
            dd($e->getMessage());
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function zoneWiseEmployeeEdit($id)
    {
        $employee = User::with(['zones', 'locations'])->findOrFail($id);
         $zoneIds     = $employee->zones->pluck('id')->toArray();  

        return response()->json([
            'id'            => $employee->id,
            'name'          => $employee->name,
            'email'         => $employee->email,
            'password'      => $employee->password,
            'mobile'        => $employee->mobile,
            'alternate_mobile'   => $employee->alternate_number,
            'role'          => $employee->role,
            'supervisor_id' => $employee->supervisor_id,
            'zone_id'       => $zoneIds,
        ]);
    }

    public function zoneWiseEmployeeUpdate(Request $request,$id){
        $validated = $request->validate([
            'edit_name'              => 'required|string|max:255',
            'edit_email'             => 'required|email|unique:users,email,' . $id,
            'edit_password'          => 'required',
            'edit_phone'             => 'required|digits:10',
            'edit_alternate_number'  => 'nullable|digits:10',
            'edit_role'              => 'required|in:supervisor,employee,complaint',
            'edit_supervisor_id' => [
                            Rule::requiredIf($request->edit_role === 'employee'),
                            'nullable',
                            'exists:users,id',
                        ],
           'edit_zone_id'   => 'required|array',
           'edit_zone_id.*' => 'required|exists:zones,id',
        ]);

        try{
             // Find employee
            $employee = User::findOrFail($id);
            // Update basic fields
            $employee->name = $validated['edit_name'];
            $employee->email            = $validated['edit_email'];
            $employee->password         = Hash::make($validated['edit_password']);
            $employee->mobile           = $validated['edit_phone'];
            $employee->alternate_number = $validated['edit_alternate_number'];
            $employee->role             = $validated['edit_role'];
            $employee->supervisor_id    = $validated['edit_supervisor_id'];
            $employee->save();

             // Update zone assignments (pivot table)
            if ($request->has('edit_zone_id')) {
                $employee->zones()->sync($validated['edit_zone_id']); 
            }

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully.',
        ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage(),
            ], 500);
        }



    }

    public function zoneWiseEmployeeDelete($id){
        try {
            $employee = User::findOrFail($id);

            // Delete associated employee zone assignments
            EmployeeZoneAssignment::where('employee_id', $id)->delete();

            // Delete employee
            $employee->delete();

            return response()->json([
                'success' => true,
                'message' => 'Employee and all their zone assignments have been deleted.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete employee.'
            ], 500);
        }
    }


    public function zoneWiseEmployeeStatus(Request $request, $id)
    {
        $employee = User::findOrFail($id);

        $newStatus = $employee->status === 'active' ? 'inactive' : 'active';

        \DB::beginTransaction();
        try {
            // Update employee status
            $employee->update(['status' => $newStatus]);

            // Update related zone assignments
            EmployeeZoneAssignment::where('employee_id', $employee->id)
                ->update(['status' => $newStatus]);

            // Update related location assignments
            EmployeeLocationAssignment::where('employee_id', $employee->id)
                ->update(['status' => $newStatus]);

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Employee and related assignments status updated to {$newStatus}.",
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage(),
            ], 500);
        }
    }



}
