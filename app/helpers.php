<?php

if (!function_exists('storage_url')) {
    /**
     * Generate URL for storage file
     * Uses route-based serving if symlink doesn't exist
     * 
     * @param string $path Path to file in storage/app/public
     * @return string
     */
    function storage_url(?string $path): string
    {
        if (empty($path)) {
            return '';
        }
        
        // Remove leading slash if present
        $path = ltrim($path, '/');
        
        // Priority 1: Check if symlink or directory exists in public/storage
        $symlinkPath = public_path('storage');
        $publicStorageFile = public_path('storage/' . $path);
        
        // If symlink exists or directory exists, use asset() (fastest method)
        if (is_link($symlinkPath) || (file_exists($symlinkPath) && is_dir($symlinkPath))) {
            // Also check if the specific file exists
            if (file_exists($publicStorageFile)) {
                return asset('storage/' . $path);
            }
        }
        
        // Priority 2: Try to use route-based URL
        try {
            $routes = \Illuminate\Support\Facades\Route::getRoutes();
            if ($routes->hasNamedRoute('storage')) {
                $url = route('storage', ['path' => $path]);
                return $url;
            }
        } catch (\Exception $e) {
            // Route not available, continue to fallback
        }
        
        // Priority 3: Fallback to direct URL (will be handled by route or StorageController)
        $url = url('/storage/' . $path);
        return $url;
    }
}

