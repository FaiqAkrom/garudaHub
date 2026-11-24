<?php

/**
 * Laravel Development Server Router
 *
 * This file is used by `php artisan serve` (PHP built-in web server).
 * Unlike Apache/Nginx, the built-in server does NOT process .htaccess.
 *
 * IMPORTANT: `return false` lets the built-in server handle static files
 * directly, but it DISCARDS any headers set before the return. So we must
 * read and serve static files ourselves to ensure security headers are sent.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Map of file extensions to MIME types for static assets
$mimeTypes = [
    'css'   => 'text/css',
    'js'    => 'application/javascript',
    'json'  => 'application/json',
    'png'   => 'image/png',
    'jpg'   => 'image/jpeg',
    'jpeg'  => 'image/jpeg',
    'gif'   => 'image/gif',
    'svg'   => 'image/svg+xml',
    'ico'   => 'image/x-icon',
    'woff'  => 'font/woff',
    'woff2' => 'font/woff2',
    'ttf'   => 'font/ttf',
    'eot'   => 'application/vnd.ms-fontobject',
    'otf'   => 'font/otf',
    'txt'   => 'text/plain',
    'xml'   => 'application/xml',
    'webp'  => 'image/webp',
    'map'   => 'application/json',
    'webmanifest' => 'application/manifest+json',
];

// Check if this is a request for a real static file
if ($uri !== '/' && file_exists($filePath = __DIR__ . '/public' . $uri) && is_file($filePath)) {
    // Determine MIME type
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $contentType = $mimeTypes[$ext] ?? (mime_content_type($filePath) ?: 'application/octet-stream');

    // Set security headers
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; font-src 'self' data:; connect-src 'self'; frame-ancestors 'self'; form-action 'self'; base-uri 'self'; object-src 'none';");
    header_remove('X-Powered-By');

    // Set content headers
    header('Content-Type: ' . $contentType);
    header('Content-Length: ' . filesize($filePath));

    // Serve the file
    readfile($filePath);
    exit;
}

// All other requests go through Laravel's front controller
require_once __DIR__ . '/public/index.php';
