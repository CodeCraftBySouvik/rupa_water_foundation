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

    //  public function store(Request $request)
    // {
    //     $request->validate([
    //         'image'   => 'required',
    //         'image.*' => 'image|max:2048',
    //     ]);

    //     $paths = [];
    //     if ($request->hasFile('image')) {
    //         foreach ($request->file('image') as $file) {
    //             $fileName = time() . rand(10000, 99999) . '.' . $file->getClientOriginalExtension();
    //             $file->move(public_path('uploads/gallery'), $fileName);
    //             $paths[] = 'uploads/gallery/' . $fileName;
    //         }
    //    }

    //     Gallery::create([
    //         'image_path' => implode(',', $paths),   // e.g. "uploads/gallery/1.jpg,uploads/gallery/2.jpg"
    //     ]);

    //     return back()->with('success', 'Gallery saved with multiple images!');
    // }

    public function store(Request $request)
    {
        // 1️⃣  Validate input
        $request->validate([
            'image'   => 'required',
            'image.*' => 'image|max:2048',
        ]);

        // 2️⃣  Save each file, collect their relative paths
        $newPaths = [];
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $file) {
                $fileName = time() . rand(10000, 99999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/gallery'), $fileName);
                $newPaths[] = 'uploads/gallery/' . $fileName;
            }
        }

        // 3️⃣  Grab (or create) the single gallery row
        //     Here we always use the FIRST record; adjust query if you
        //     want a specific ID or per‑user gallery.
        $gallery = Gallery::first();          // returns null if table empty

        if (!$gallery) {
            // No gallery yet ➜ create a new row
            $gallery = Gallery::create([
                'image_path' => implode(',', $newPaths),
            ]);
        } else {
            // Row exists ➜ append the new paths
            $existing = $gallery->image_path
                ? explode(',', $gallery->image_path)
                : [];

            // merge + remove accidental duplicates
            $merged = array_unique(array_merge($existing, $newPaths));

            $gallery->update([
                'image_path' => implode(',', $merged),
            ]);
        }

        return back()->with('success', 'Images added to gallery!');
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
