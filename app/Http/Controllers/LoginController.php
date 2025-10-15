<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    /**
     * Display login page.
     *
     * @return Renderable
     */
    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();

            return redirect()->route('zone.index');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }


    public function changePassword(){
        return view('auth.change-password');
    }

    public function changePasswordUpdate(Request $request){
        $request->validate([
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'password' => 'required|confirmed',
        ]); 

        // Get Logged in User
        $user = Auth::guard('web')->user();

        if(!$user){
            return redirect()->route('login')->with('error','No authenticated user found');
        }

        // Update email and password
        $user->email = $request->email ?? null;
        $user->password = Hash::make($request->password);
        $user->save();

         return redirect()->route('change.password')->with('success', 'Email and password updated successfully. Please log in again.');

    }
}
