<?php

/*
| Web-visible storage root (no is_dir here — open_basedir on shared hosting).
|
| Production (.env): set PUBLIC_STORAGE_ROOT under /home/shivibes/..., e.g.
|   /home/shivibes/domains/shivibes.com/public_html/storage
|
| Or USE_PUBLIC_HTML_STORAGE=true to use base_path('public_html/storage').
| Local dev: leave both unset → storage/app/public
*/
$sitePublicStorageRoot = env('PUBLIC_STORAGE_ROOT');

if (! $sitePublicStorageRoot && filter_var(env('USE_PUBLIC_HTML_STORAGE', false), FILTER_VALIDATE_BOOLEAN)) {
    $sitePublicStorageRoot = base_path('public_html/storage');
}

if (! $sitePublicStorageRoot) {
    $sitePublicStorageRoot = storage_path('app/public');
}

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        /** Admin uploads + any file that must be served from /storage/... */
        'site_public' => [
            'driver' => 'local',
            'root' => $sitePublicStorageRoot,
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => array_merge(
        [
            public_path('storage') => storage_path('app/public'),
        ],
        filter_var(env('USE_PUBLIC_HTML_STORAGE', false), FILTER_VALIDATE_BOOLEAN)
            ? [base_path('public_html/storage') => storage_path('app/public')]
            : []
    ),

];
