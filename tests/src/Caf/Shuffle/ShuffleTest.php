<?php

namespace Caf\Shuffle;

class ShuffleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider providerForPackFormats
     */
    public function testIfDetectsTheCorrectPackFormatAndLength($format, $numbers, $bytes)
    {
        $sf = new Shuffle($numbers);
        $this->assertEquals($format, $sf->getPackFormat());
        $this->assertEquals($bytes, $sf->getPackLength());
    }

    public function testShuffle()
    {

        $itens = 512;

        $sf = new Shuffle($itens);
        $sf->shuffle();

        $iterator = $sf->getIterator();
        $this->assertInstanceOf('ArrayAccess', $iterator);
        $this->assertInstanceOf('SeekableIterator', $iterator);
        $this->assertInstanceOf('Countable', $iterator);

        $this->assertEquals($itens, count($iterator));

        $expected = range(0, $itens - 1);
        $data = iterator_to_array($iterator);

        $diff = array_diff($expected, $data);

        $this->assertEmpty(array_diff($expected, $data));
    }

    public function providerForPackFormats()
    {
        return array(
            array('C', 10, 1),
            array('C', 255, 1),
            array('S', 256, 2),
            array('S', 65534, 2),
            array('L', 65536, 4),
            array('L', 4294967295, 4),
            array('d', 4294967296, 8),
            array('d', 1000000000000, 8),
            array('d', 1000000000000000, 8),
            array('d', 1000000000000000000, 8),
        );

    }
}
