<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        
        // Log the request
        Log::debug('StorageController: Requesting file', [
            'path' => $path,
            'full_path' => storage_path('app/public/' . $path),
        ]);
        
        // Check if file exists in public disk
        if (!Storage::disk('public')->exists($path)) {
            Log::warning('StorageController: File not found', [
                'path' => $path,
                'storage_path' => storage_path('app/public/' . $path),
                'file_exists' => file_exists(storage_path('app/public/' . $path)),
            ]);
            abort(404, 'File not found: ' . $path);
        }

        try {
            // Get file content and mime type
            $file = Storage::disk('public')->get($path);
            $mimeType = Storage::disk('public')->mimeType($path);
            
            if ($file === false) {
                Log::error('StorageController: Failed to read file', [
                    'path' => $path,
                ]);
                abort(500, 'Failed to read file');
            }

            // Return file response with proper headers
            return response($file, 200)
                ->header('Content-Type', $mimeType ?: 'application/octet-stream')
                ->header('Content-Disposition', 'inline; filename="' . basename($path) . '"')
                ->header('Cache-Control', 'public, max-age=31536000');
        } catch (\Exception $e) {
            Log::error('StorageController: Error serving file', [
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            abort(500, 'Error serving file: ' . $e->getMessage());
        }
    }
}

