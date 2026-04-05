<?php

declare(strict_types=1);

/**
 * Web path to the folder that contains index.php (e.g. /TrailConnect/public).
 * Empty string => use relative asset URLs (works when the app lives at domain root).
 */
if (!function_exists('tc_public_base')) {
    function tc_public_base(): string
    {
        $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        $script = str_replace('\\', '/', (string) $script);
        $dir = dirname($script);
        if ($dir === '/' || $dir === '\\' || $dir === '.') {
            return '';
        }

        return rtrim($dir, '/');
    }
}

if (!function_exists('tc_asset_url')) {
    /** Path relative to public/ e.g. assets/css/style.css */
    function tc_asset_url(string $path): string
    {
        $path = ltrim(str_replace('\\', '/', $path), '/');
        $base = tc_public_base();

        return $base === '' ? $path : $base . '/' . $path;
    }
}

if (!function_exists('tc_url')) {
    /** Same-origin URL to index.php with optional query (e.g. page=login). */
    function tc_url(string $query = ''): string
    {
        $base = tc_public_base();
        $index = ($base === '' ? '' : $base) . '/index.php';
        $query = ltrim($query, '?&');

        return $query === '' ? $index : $index . '?' . $query;
    }
}
