<?php

require_once "../vendor/autoload.php";

$r = new Caf\Shuffle\Shuffle(100);
$r->shuffle();

$r->exportToFile('rand.bin');