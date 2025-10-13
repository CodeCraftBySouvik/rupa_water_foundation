<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class Helpers
{
    public static function isSupervisor()
    {
        $user = Auth::user();
        return $user && $user->role === 'supervisor';
    }

   
}
