<?php
// Skrip smoke check sederhana: membuat request internal ke beberapa route
// dan melaporkan HTTP status / exception. Jalankan dengan PHP CLI.

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$routes = [
    '/',
    '/arsip',
    '/arsip/create',
    '/klasifikasi',
    '/lokasi',
    '/lemari',
    '/rak',
    '/peminjaman',
    '/peminjaman/create',
    '/retensi',
    '/pengguna',
    '/login',
];

foreach ($routes as $uri) {
    try {
        $request = Illuminate\Http\Request::create($uri, 'GET');
        $response = $kernel->handle($request);
        $code = $response->getStatusCode();
        $len = strlen((string) $response->getContent());
        echo str_pad($uri, 30) . " -> HTTP $code, content-length: $len\n";
        $kernel->terminate($request, $response);
    } catch (Throwable $e) {
        echo str_pad($uri, 30) . " -> EXCEPTION: " . $e->getMessage() . "\n";
        echo $e->getTraceAsString() . "\n\n";
    }
}

echo "Smoke check selesai.\n";
