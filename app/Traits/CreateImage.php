<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait CreateImage
{
    public function createImage($request) {
        $file = $request->file('image');

        // Get filename without extension.
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME); 

        // Get file extension.
        $extension = $file->getClientOriginalExtension(); 

        $directory = 'products'; 

        // Start with original name.
        $filename = $originalName . '.' . $extension; 
        $counter = 1;

        // Check if file exists, and modify filename if necessary.
        while (Storage::disk('public')->exists($directory . '/' . $filename)) {
            $filename = $originalName . '-' . $counter . '.' . $extension;
            $counter++;
        }

        // Store the file with the unique filename.
        return $file->storeAs($directory, $filename, 'public');
    }
}