<?php
$dir = __DIR__ . '/../database/migrations';
$files = glob($dir . '/*.php');

$migrations = [];
foreach ($files as $f) {
    $migrations[basename($f)] = file_get_contents($f);
}

$creates = [];
foreach ($migrations as $name => $content) {
    if (preg_match("/Schema::create\(\s*'([^']+)'/", $content, $m)) {
        $table = $m[1];
        $creates[$table] = $content;
    }
}

$fks = [];
foreach ($migrations as $name => $content) {
    // find foreign definitions
    if (preg_match_all("/->foreign\(\s*'([^']+)'\s*\)->references\(\s*'([^']+)'\s*\)->on\(\s*'([^']+)'\s*\)/", $content, $ms, PREG_SET_ORDER)) {
        foreach ($ms as $m) {
            $fks[] = [
                'file' => $name,
                'column' => $m[1],
                'references' => $m[2],
                'on' => $m[3],
            ];
        }
    }
}

$problems = [];
foreach ($fks as $fk) {
    $refTable = $fk['on'];
    $refCol = $fk['references'];
    if (!isset($creates[$refTable])) {
        $problems[] = "Referenced table '{$refTable}' (from {$fk['file']}) not created in migrations.";
        continue;
    }
    $content = $creates[$refTable];
    // look for id(), id('name'), primary('name'), or column definition for refCol
    $found = false;
    if (preg_match("/\->id\(\s*\)/", $content) && $refCol === 'id') {
        $found = true;
    }
    if (!$found && preg_match("/\->id\(\s*'{$refCol}'\s*\)/", $content)) {
        $found = true;
    }
    if (!$found && preg_match("/->primary\(\s*'{$refCol}'\s*\)/", $content)) {
        $found = true;
    }
    if (!$found && preg_match("/\b{$refCol}\b.*->.*primary\(|\b{$refCol}\b.*->.*unsignedBigInteger\(/", $content)) {
        $found = true;
    }

    if (!$found) {
        $problems[] = "Foreign key in {$fk['file']} references '{$refTable}.{$refCol}' but that column was not found in migration that creates '{$refTable}'.";
    }
}

if (empty($problems)) {
    echo "No obvious FK mismatches detected.\n";
    exit(0);
}

echo "Potential FK problems:\n";
foreach ($problems as $p) {
    echo " - $p\n";
}

exit(0);
