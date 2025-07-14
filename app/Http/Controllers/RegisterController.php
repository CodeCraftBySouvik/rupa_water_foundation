<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
// use App\Http\Requests\RegisterRequest;
use App\Models\User;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $attributes = request()->validate([
            'name' => 'required|max:255|min:2',
            'mobile'   => [
                'required',
                'regex:/^[6-9]\d{9}$/',  // ensures 10 digits starting with 6–9
                'unique:users,mobile',
            ],
            'email' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required|min:5|max:255',
        ]);
        $user = User::create($attributes);
        auth()->login($user);

        return redirect('/dashboard');
    }
}
