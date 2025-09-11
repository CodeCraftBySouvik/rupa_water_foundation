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
                    'id'            => $loc->id,      
                    'location_name' => $loc->location_name,
                    'status'        => $loc->status,
                ];
            }),
        ]);
    }


    // Zone wise location
    public function downloadSampleCsv(){
         $columns = ['Zone_name', 'Location_name', 'Opening_date', 'Size'];

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

    public function import(Request $request){
        $request->validate([
            'file' => 'required|mimetypes:text/plain,text/csv,application/csv,text/comma-separated-values,application/vnd.ms-excel',
        ]);
        
        try {
                $path = $this->file->getRealPath();
                $file = fopen($path, 'r');

                $header = fgetcsv($file); // Skip header row

                while ($row = fgetcsv($file)) {
                    // Basic row validation
                    if (count($row) < 5) {
                        throw new \Exception('Invalid row format.');
                    }

                    $zoneName      = trim($row[0]); 
                    $locationId    = trim($row[1]); 
                    $title         = trim($row[2]); 
                    $position      = trim($row[3]); 
                    $openingDate   = trim($row[4]); 

                    // Get Zone by name (or throw error if not found)
                    $zone = \App\Models\Zone::where('name', $zoneName)->first();
                    if (!$zone) {
                        throw new \Exception("Zone '{$zoneName}' not found.");
                    }

                    // Validate opening_date format
                    $parsedDate = \DateTime::createFromFormat('d.m.y', $openingDate);
                    if (!$parsedDate) {
                        throw new \Exception("Invalid date format at row: {$zoneName}, {$openingDate}");
                    }

                    // Create ZoneWiseLocation entry
                    \App\Models\ZoneWiseLocation::create([
                        'zone_id'       => $zone->id,
                        'location_id'   => $locationId,
                        'title'         => $title,
                        'position'      => (int) $position,
                        'opening_date'  => $parsedDate->format('Y-m-d'),
                        'status'        => 'active',
                    ]);
                }

                fclose($file);

                session()->flash('success', 'CSV imported successfully.');

            } catch (\Exception $e) {
                dd($request->getMessage());
                session()->flash('import_errors', [
                    ['row' => $row ?? [], 'errors' => [$e->getMessage()]]
                ]);
            }

    }

    public function zoneWiseLocationIndex(Request $request){
        $getZones = Zone::latest()->get();
        $getLocations = Location::all();
         $locations = ZoneWiseLocation::with('location_details')->latest()->get();
         
         $editLocation = null;
         if($request->has('edit')){
            $editLocation = ZoneWiseLocation::findOrFail($request->edit);
         }
        return view('admin.zone.location.index',compact('getZones','getLocations','locations','editLocation'));
    }

    public function zoneWiseLocationStore(Request $request){
         $request->validate([
            'zone_id'       => 'required|exists:zones,id',
            'location_id' => 'required|string|max:255'
         ]);

         ZoneWiseLocation::create($request->all());
         return redirect()->back()->with('success', 'Zone Location created successfully.');
    }

    public function zoneWiseLocationUpdate(Request $request,$id){
        $request->validate([
            'zone_id'       => 'required|exists:zones,id',
            'location_id' => 'required|string|max:255',
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
            'locations:id,location_name' // multiple locations if assigned
        ])
        ->select('id', 'name', 'mobile', 'email', 'password', 'role', 'status', 'supervisor_id')
        ->where('name', '!=', 'Super Admin') 
        ->where('role', '!=', 'ho') 
        ->get();

        return view('admin.zone.location.employee.index',compact('supervisors','zones','userlist'));
    }

    public function zoneWiseEmployeeStore(Request $request){
       $validated = $request->validate([
                        'name'         => 'required|string|max:255',
                        'email'        => 'required|email|unique:users,email',
                        'password'     => 'required|min:6',
                        'phone'        => 'required|digits:10',
                        'role'         => 'required|in:ho,supervisor,employee,complaint',
                        'supervisor_id'=> 'nullable|exists:users,id',
                        'zone_id'      => 'required|exists:zones,id',
                        'location_id'  => 'required|exists:zone_wise_locations,id',
                    ]);

         \DB::beginTransaction();
        try {
            // Create Employee (assuming your User model holds employees too)
            $employee = User::create([
                'name'         => $validated['name'],
                'email'        => $validated['email'],
                'password'     => Hash::make($validated['password']),
                'mobile'        => $validated['phone'],
                'role'         => $validated['role'],
                'supervisor_id'=> $validated['supervisor_id'] ?? null,
                'status'       => 'active',
            ]);

            // Assign Zone
            EmployeeZoneAssignment::create([
                'employee_id'   => $employee->id,
                'zone_id'       => $validated['zone_id'],
                'status'       => 'active',
                'assigned_date'=> now()->toDateString(),
            ]);

            // Assign Location
            EmployeeLocationAssignment::create([
                'employee_id'   => $employee->id,
                'zone_id'       => $validated['zone_id'],
                'location_id'   => $validated['location_id'],
                'status'       => 'active',
                'assigned_date'=> now()->toDateString(),
            ]);

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
