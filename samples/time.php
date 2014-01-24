<?php

require_once "../vendor/autoload.php";

function getMemoryUsage() {
    return number_format(memory_get_usage() / 1024 / 1024, 2, '.', '') . "Mb";
}

echo 'Generating 10000000 (10 million) numbers' . PHP_EOL;

$start = time();
//$random = range(0, 10000000);
//shuffle($random);

echo 'range + shuffe: ' . getMemoryUsage() . ' in ' . (time() - $start) . ' seconds' . PHP_EOL;

unset($random);

$start = time();
$r = new Caf\Shuffle\Shuffle(100000000);
$r->shuffle();

echo 'Caf\Random: ' . getMemoryUsage() . ' in ' . (time() - $start) . ' seconds' . PHP_EOL;