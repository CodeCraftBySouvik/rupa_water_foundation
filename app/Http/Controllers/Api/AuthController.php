<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // public function register(Request $request){
    //     $data = $request->validate([
    //         'name'     => 'required|string',
    //          'mobile'   => [
    //             'required',
    //             'regex:/^[6-9]\d{9}$/',  // ensures 10 digits starting with 6–9
    //             'unique:users,mobile',
    //         ],
    //         'email'    => 'required|email|unique:users',
    //         'password' => 'required|min:6',
    //     ]);

    //     $user = User::create([
    //         'name'     => $data['name'],
    //         'mobile'     => $data['mobile'],
    //         'email'    => $data['email'],
    //         'password' => Hash::make($data['password']),
    //     ]);

    //     $token = auth('api')->login($user);
    //     return response()->json([
    //         'message' => 'Register Successfully',
    //         'token' => $token,
    //         'user'  => $user
    //     ]);

    // }

     public function store(Request $request)
    {
        /* ---------- 1. Validate input ---------- */
        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:255',
            'mobile' => [
                'required',
                'regex:/^[6-9]\d{9}$/',     // Indian 10‑digit mobile
                'unique:users,mobile',
            ],
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        /* ---------- 2. Create user ---------- */
        $user = User::create([
            'name'     => $request->name,
            'mobile'   => $request->mobile,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        /* ---------- 3. Optional: issue a JWT (comment out if you don’t want it) ---------- */
        $token = auth('api')->login($user);

        /* ---------- 4. Return JSON ---------- */
        return response()->json([
            'status'  => true,
            'message' => 'User created successfully',
            'user'    => $user,
            'token' => $token   // include only if you kept step 3
        ], 201);
    }

    // public function login(Request $request){
    //      $validator = Validator::make($request->all(), [
    //         'email'    => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }
    //     $user = User::where('email',$request->email)->first();
    //     // Verify password with Hash::check()     
    //     if(!$user && !Hash::check($request->password,$user->password)){
    //         return response()->json([
    //             'error'=> 'Invalid Credentials'
    //         ],401);
    //     }

    //     // Create & return JWT
    //     $token = auth('api')->login($user);
    //     return response()->json([
    //         'message' => 'Login Successfully',
    //         'token' => $token,
    //         'user'  => $user
    //     ]);
    // }

    public function login(Request $request)
    {
        // 1.  Validate *mobile* instead of email
        $validator = Validator::make($request->all(), [
            'mobile'   => ['required', 'regex:/^[6-9]\d{9}$/'],  // 10‑digit Indian mobile
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Find user by mobile
        $user = User::where('mobile', $request->mobile)->first();

        // 3.  Correct logic: if user not found OR password mismatch => 401
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Invalid credentials'
            ], 401);
        }

        // 4.  Create & return JWT
        $token = auth('api')->login($user);

        return response()->json([
            'message' => 'Login Successfully',
            'token'   => $token,
            'user'    => $user
        ]);
    }

    

    


}
