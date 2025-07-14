<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserController extends Controller
{
    //

    public function index() {
        $userlist = User::latest()->get();
        return view('admin.user.index',compact('userlist'));
    }
}
