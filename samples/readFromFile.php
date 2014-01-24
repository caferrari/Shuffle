<?php

require_once "../vendor/autoload.php";

if (!file_exists('rand.bin')) {
	die('Run the exportToFile sample before');
}

$r = new Caf\Shuffle\Shuffle(100);
$r->loadFile('rand.bin');

foreach ($r->getIterator() as $number) {
    echo $number . PHP_EOL;
}