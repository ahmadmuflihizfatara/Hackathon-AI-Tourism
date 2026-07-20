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



    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
        'embedding_model' => env('GEMINI_EMBEDDING_MODEL', 'gemini-embedding-2'),
    ],

    'chromadb' => [
        'url' => env('CHROMA_DB_URL', 'http://localhost:8000/api/v1'),
        'collection' => env('CHROMA_DB_COLLECTION', 'tourism_gemini_db'),
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
