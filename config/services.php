<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // ── AI ─────────────────────────────────────────────────────────────────

    'lmstudio' => [
        'url' => env('LM_STUDIO_API_URL', 'http://localhost:1234/v1'),
        'model' => env('LM_STUDIO_MODEL', 'gemma-4-e4b'),
        'embedding_model' => env('LM_STUDIO_EMBEDDING_MODEL', 'bge-m3'),
    ],

    // ── Image APIs (fallback chain: Unsplash → Pexels → Wikimedia) ─────────

    /**
     * Unsplash — sumber utama gambar destinasi
     * Kuota gratis: 50 request/jam
     * Daftar: https://unsplash.com/developers
     */
    'unsplash' => [
        'access_key' => env('UNSPLASH_ACCESS_KEY'),
    ],

    /**
     * Pexels — sumber backup gambar destinasi
     * Kuota gratis: 200 request/jam, 20.000/bulan
     * Daftar: https://www.pexels.com/api/
     */
    'pexels' => [
        'api_key' => env('PEXELS_API_KEY'),
    ],

];
