<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gallery;

class GalleryController extends Controller
{
    public function gallery(){
         $galleryItems = Gallery::all();
        return view('admin.gallery.index',compact('galleryItems'));
    }

     public function store(Request $request)
    {
        $request->validate([
            'image'   => 'required',
            'image.*' => 'image|max:2048',
        ]);

        $paths = [];
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $file) {
                $fileName = time() . rand(10000, 99999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/gallery'), $fileName);
                $paths[] = 'uploads/gallery/' . $fileName;
            }
       }

        Gallery::create([
            'image_path' => implode(',', $paths),   // e.g. "uploads/gallery/1.jpg,uploads/gallery/2.jpg"
        ]);

        return back()->with('success', 'Gallery saved with multiple images!');
    }

    public function deleteImage(Gallery $gallery, $index)
    {
        $paths = explode(',', $gallery->image_path);
        $file  = $paths[$index] ?? null;
        unset($paths[$index]);

        //Remove empty values and reindex
        $paths = array_values(array_filter($paths));

        // $gallery->update(['image_path' => implode(',', $paths)]);

        if ($file && file_exists(public_path($file))) {
            unlink(public_path($file));
        }

        if(count($paths) === 0){
            //all images deleted then gallery records too
            $gallery->delete();

            return back()->with('success', 'Image and gallery deleted');
        } else{
            $gallery->update(['image_path' => implode(',', $paths)]);

            return back()->with('success', 'Image deleted.');
        }

        
    }


}
