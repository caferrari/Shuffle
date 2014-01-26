<?php

require_once "../vendor/autoload.php";

function getMemoryUsage() {
    return number_format(memory_get_usage() / 1024 / 1024, 2, '.', '') . "MB";
}

echo 'Generating 1000000000 (100 million) numbers' . PHP_EOL;

$start = time();
$r = new Caf\Shuffle\Shuffle(100000000);
$r->shuffle();

echo 'Caf\Shuffle: ' . getMemoryUsage() . ' in ' . (time() - $start) . ' seconds' . PHP_EOL;