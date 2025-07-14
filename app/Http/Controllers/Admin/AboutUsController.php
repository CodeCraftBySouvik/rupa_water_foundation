<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AboutUs;

class AboutUsController extends Controller
{
    public function about_us(){
        $about = AboutUs::first();
         $aboutUs  = $about;      
        return view('admin.about_us.index',compact('about','aboutUs'));
    }

    public function about_us_update(Request $request,$id){
        $request->validate([
            'title' => 'required',
            'description' => 'required'
        ]);
         $about = AboutUs::findOrFail($id);
         $about->update($request->all());
         return redirect()->route('about_us.index')->with('success', 'About Us updated successfully');
    }

   


}
