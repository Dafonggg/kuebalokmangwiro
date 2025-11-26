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
        
        // Check if symlink exists (for development/local environments)
        $symlinkPath = public_path('storage');
        if (is_link($symlinkPath) || (file_exists($symlinkPath) && is_dir($symlinkPath))) {
            // Use asset() if symlink exists
            return asset('storage/' . $path);
        }
        
        // Use route-based URL if symlink doesn't exist
        try {
            $url = route('storage', ['path' => $path]);
            \Illuminate\Support\Facades\Log::info('storage_url generated', [
                'path' => $path,
                'url' => $url,
            ]);
            return $url;
        } catch (\Exception $e) {
            // Fallback to direct URL if route not available
            $url = url('/storage/' . $path);
            \Illuminate\Support\Facades\Log::warning('storage_url fallback used', [
                'path' => $path,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return $url;
        }
    }
}

