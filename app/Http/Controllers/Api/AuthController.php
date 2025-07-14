<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request){
        $data = $request->validate([
            'name'     => 'required|string',
             'mobile'   => [
                'required',
                'regex:/^[6-9]\d{9}$/',  // ensures 10 digits starting with 6–9
                'unique:users,mobile',
            ],
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'mobile'     => $data['mobile'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = auth('api')->login($user);
        return response()->json([
            'token' => $token,
            'user'  => $user
        ]);

    }
}
