<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserController extends Controller
{
    //

    public function index() {
        $userlist = User::where('id', '!=', Auth::id())->latest()->get();
        return view('admin.user.index',compact('userlist'));
    }

    public function create() {
       return view('admin.user.create'); 
    }

    public function store(Request $request) {
        $attributes = request()->validate([
        'name' => 'required|max:255|min:2',
        'mobile'   => [
            'required',
            'regex:/^[6-9]\d{9}$/',  // ensures 10 digits starting with 6–9
            'unique:users,mobile',
        ],
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:5|max:255',
        ]);

        // Manually hash the password
        $attributes['password'] = Hash::make($attributes['password']);
        $user = User::create($attributes);
        // auth()->login($user);

       return redirect()->route('user.index')->with('success','User created successfully.');
    }

    public function edit($id) {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    public function update(Request $request) {
        $user = User::findOrFail($request->id);
        $request->validate([
            'name' => 'required|max:255|min:2',
            'mobile' => [
                'required',
                'regex:/^[6-9]\d{9}$/',
                'unique:users,mobile,' . $user->id,
            ],
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
        ]);
        return redirect()->route('user.index')->with('success', 'User updated successfully');
    }
}
