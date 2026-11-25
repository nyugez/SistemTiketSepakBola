<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supabase API & Project Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi ini digunakan untuk menghubungkan aplikasi Laravel ke
    | Supabase Client API, REST API (PostgREST), dan Supabase Storage.
    |
    */

    'url' => env('SUPABASE_URL', ''),

    // Anon / Public API Key
    'key' => env('SUPABASE_KEY', env('SUPABASE_ANON_KEY', '')),

    // Service Role Key (untuk akses server-side dengan hak istimewa/bypass RLS jika diperlukan)
    'service_role_key' => env('SUPABASE_SERVICE_ROLE_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Supabase Storage Settings
    |--------------------------------------------------------------------------
    |
    | Pengaturan default bucket untuk berkas seperti E-Ticket PDF dan QR Code.
    |
    */

    'storage' => [
        'bucket' => env('SUPABASE_STORAGE_BUCKET', 'tickets'),
    ],

];
