<?php

require_once "../vendor/autoload.php";

$r = new Caf\Shuffle\Shuffle(10);
$r->shuffle();

foreach ($r->getIterator() as $number) {
    echo $number . PHP_EOL;
}