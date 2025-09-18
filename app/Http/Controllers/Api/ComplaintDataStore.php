<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintDataStore extends Controller
{
    public function complaintInfoStore(Request $request){
            try{
            $request->validate([
                'description' => 'required|string|max:1000',
                'images'      => 'required|array',
                'images.*'    => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $userId = auth()->id();
            $images = $request->file('images');

           
            $imagePaths = [];
            
            foreach ($images as $image) {
                // if($image->isValid()){
                    $fileName = time() . rand(1000,9999). '.' .$image->extension();
                    $image->move(public_path('uploads/complaint/'), $fileName);
                    $filePath = 'uploads/complaint/' . $fileName;

                    // Store relative path
                    $imagePaths[] = $filePath;

                // }
            }
        

            $complaint = Complaint::create([
                'user_id'     => $userId,
                'description' => $request->description,
                'images'      => json_encode($imagePaths),
            ]);

            // Return response with decoded image paths
            return response()->json([
                'status'    => true,
                'message'   => 'Complaint stored successfully',
                'complaint' => [
                    'id'          => $complaint->id,
                    'user_id'     => $complaint->user_id,
                    'description' => $complaint->description,
                      'images'      => collect(json_decode($complaint->images))->map(fn($path) => url($path)),
                ],
            ], 200);
        }catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
