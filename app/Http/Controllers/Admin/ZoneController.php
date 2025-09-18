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
        $request->validate([
            'file' => 'required|mimetypes:text/plain,text/csv,application/csv,text/comma-separated-values,application/vnd.ms-excel',
        ]);

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
                    throw new \Exception('Invalid row format.');
                }

                $zoneName      = trim($row[0]); 
                $locationTitle = trim($row[1]); 
                $openingDate   = trim($row[2]); 
                $size         = trim($row[3]); 

                // Find the Zone by name
                $zone = Zone::where('name', $zoneName)->first();
                if (!$zone) {
                    throw new \Exception("Zone '{$zoneName}' not found.");
                }

                // Find Location by title
                $location = Location::where('title', $locationTitle)->first();
                if (!$location) {
                    throw new \Exception("Location '{$locationTitle}' not found.");
                }

                // Check if this zone-location combination already exists
                $exists = ZoneWiseLocation::where('zone_id', $zone->id)
                            ->where('location_id', $location->id)
                            ->exists();

                if ($exists) {
                    // Skip this row if already exists
                    continue;
                }

                $parsedDate = \DateTime::createFromFormat('d.m.y', $openingDate);
                if (!$parsedDate) {
                    throw new \Exception("Invalid date format at row: {$zoneName}, {$openingDate}");
                }

                ZoneWiseLocation::create([
                    'zone_id'       => $zone->id,
                    'location_id'   => $location->id,   // Match location by title
                    'position'          => $size,
                    'opening_date'  => $parsedDate->format('Y-m-d'),
                    'status'        => 'active',
                ]);
            }

            fclose($fileHandle);
            return redirect()->back()->with('success', 'CSV imported successfully.');

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }



    public function zoneWiseLocationIndex(Request $request){
        $getZones = Zone::latest()->get();
        $getLocations = Location::all();
         $locations = ZoneWiseLocation::with('location_details')->latest()->paginate(20);
         
         $editLocation = null;
         if($request->has('edit')){
            $editLocation = ZoneWiseLocation::findOrFail($request->edit);
         }
        return view('admin.zone.location.index',compact('getZones','getLocations','locations','editLocation'));
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

    public function zoneWiseLocationUpdate(Request $request,$id){
       $validated = $request->validate([
            'zone_id'       => 'required|exists:zones,id',
            'location_id' => 'required|string|max:255',
       ]);

        // Check if this zone-location combination already exists
        $exists = ZoneWiseLocation::where('zone_id', $validated['zone_id'])
                    ->where('location_id', $validated['location_id'])
                    ->where('id', '!=', $id)
                    ->exists();

        if($exists){
            return redirect()->back()
                    ->with('error','This Zone and Location combination already exists.')
                    ->withInput(['zone_id' => '', 'location_id' => '']);
        }

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

        $zoneId     = $employee->zones->first()->id ?? null;
        $locationId = $employee->locations->first()->id ?? null;

        return response()->json([
            'id'            => $employee->id,
            'name'          => $employee->name,
            'email'         => $employee->email,
            'mobile'        => $employee->mobile,
            'role'          => $employee->role,
            'supervisor_id' => $employee->supervisor_id,
            'zone_id'       => $zoneId,
            'location_id'   => $locationId,
        ]);
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
