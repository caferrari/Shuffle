Caf\Shuffle
===========

This lib was made with the objective to shuffle a huge amount of numbers

Performance
-----------

It's a lot slower than the combo range + shuffle functions but it does have a nice memory advantage:

10000000 (10 million) numbers
    range + shuffe: 1425.28 MB in 9 seconds
    Caf\Shuffle: 38.59 MB in 51 seconds // Exported file: 40MB

100000000 (100 million) numbers
    range + shuffe: PHP Fatal error:  Allowed memory size of 4294967296 bytes exhausted (tried to allocate 32 bytes)
    Caf\Shuffle: 381.79Mb in 518 seconds // Exported file: 400MB

Advantages
----------

You can export the generated numbers in a nice binary file to be used later or in another place so it becomes more useful

    $r = new Caf\Shuffle\Shuffle(10000000);
    $r->initialize();
    $r->shuffle();
    $r->exportToFile('rand.bin');

It's very easy to reuse later:

    $r = new Caf\Shuffle\Shuffle(10000000);
    $r->loadFile('rand.bin');

    foreach ($r->getIterator() as $number) {
        echo $number . PHP_EOL
    }
