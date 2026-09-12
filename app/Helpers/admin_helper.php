<?php

if (! function_exists('admin_path')) {
    /**
     * Relative admin panel path (no leading slash).
     * e.g. axSdwfwgms or axSdwfwgms/products
     */
    function admin_path(string $uri = ''): string
    {
        $base = trim((string) (config('Admin')->path ?: 'axSdwfwgms'), '/');
        $uri  = trim($uri, '/');

        return $uri === '' ? $base : $base . '/' . $uri;
    }
}

if (! function_exists('admin_url')) {
    /**
     * Absolute admin panel URL via site_url().
     */
    function admin_url(string $uri = ''): string
    {
        return site_url(admin_path($uri));
    }
}
