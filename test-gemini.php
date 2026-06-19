<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(\App\Services\GeminiService::class);
$result = $service->chat([
    ['role' => 'user', 'parts' => [['text' => 'Ke bali dong 5 hari budget 3 juta']]]
]);

print_r($result);
