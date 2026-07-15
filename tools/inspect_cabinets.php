<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Location;
use App\Models\Cabinet;

echo "Locations:\n";
$locations = Location::orderBy('ruangan')->get();
foreach ($locations as $loc) {
    echo "- id={$loc->id} name={$loc->nama_lokasi}\n";
}

echo "\nCabinets (lemari):\n";
$cabs = Cabinet::orderBy('lemari_nama')->get();
foreach ($cabs as $c) {
    echo "- lemari_id={$c->lemari_id} nama={$c->lemari_nama} ruangarsip_id={$c->ruangarsip_id}\n";
}

echo "\nCabinets grouped by location:\n";
foreach ($locations as $loc) {
    $items = Cabinet::where('ruangarsip_id', $loc->id)->get();
    echo "Location {$loc->id} ({$loc->nama_lokasi}) -> " . $items->count() . " cabinets\n";
}
