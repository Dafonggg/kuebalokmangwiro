<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class StorageController extends Controller
{
    /**
     * Serve files from storage/app/public
     * 
     * @param string $path
     * @return \Illuminate\Http\Response
     */
    public function show(string $path)
    {
        // Security: Prevent directory traversal
        $path = str_replace('..', '', $path);
        
        // Check if file exists in public disk
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }

        // Get file content and mime type
        $file = Storage::disk('public')->get($path);
        $mimeType = Storage::disk('public')->mimeType($path);

        // Return file response with proper headers
        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . basename($path) . '"')
            ->header('Cache-Control', 'public, max-age=31536000');
    }
}

