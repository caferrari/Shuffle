<?php

namespace Caf\Shuffle;

use \RangeException,
    \OutOfRangeException;

class Shuffle
{

    private $max;
    private $filePointer;
    private $packFormat;
    private $packLength;
    private $initialized = false;
    private $seeder;

    public function __construct($max)
    {
        if ($max <= 0) {
            throw new RangeException("This number is too small");
        }

        $this->max = $max;
    }

    public function getPackFormat()
    {

        if (!is_null($this->packFormat)) {
            return $this->packFormat;
        }

        $sizes = array(
            'C' => pow(2, 8),
            'S' => pow(2, 16),
            'L' => pow(2, 32),
            'd' => pow(2, 64)
        );

        foreach ($sizes as $format => $size) {
            if ($this->max < $size) {
                return $this->packFormat = $format;
            }
        }

        throw new RangeException("This number is too big");

    }

    public function getPackLength()
    {

        if (!is_null($this->packLength)) {
            return $this->packLength;
        }

        $packs = array(
            'C' => 1,
            'S' => 2,
            'L' => 4,
            'd' => 8,
        );

        if (isset($packs[$this->packFormat])) {
            return $this->packLength = $packs[$this->packFormat];
        }

        throw new OutOfRangeException("Invalid pack format");
    }

    private function pack($number)
    {
        return pack($this->packFormat, $number);
    }

    public function setSeeder(Seed\Seedable $seeder)
    {
        $this->seeder = $seeder;
    }

    public function getSeeder()
    {
        if (is_null($this->seeder)) {
            $this->seeder = new Seed\Sequential;
        }

        $this->seeder->setMax($this->max);

        return $this->seeder;
    }

    public function initialize($filePointer = null)
    {

        if ($this->initialized) {
            return;
        }

        $this->getPackFormat();

        if (is_null($filePointer)) {
            $filePointer = fopen('php://memory', 'r+');
        }

        if (!is_resource($filePointer)) {
            throw new InvalidArgumentException('Invalid resouce');
        }

        if (false === $this->isSeekable($filePointer)) {
            throw new InvalidArgumentException('The stream must be opened with r+ mode');
        }

        $seeder = $this->getSeeder();

        $this->filePointer = $filePointer;
        for ($x = 0; $x < $this->max; $x++) {
            fwrite($this->filePointer, $this->pack($seeder->getNext()));
        }

        $this->initialized = true;
    }

    public function isSeekable($filePointer)
    {
        $parameters = stream_get_meta_data($filePointer);
        return $parameters['seekable'];
    }

    private function getRandomPosition()
    {
        return mt_rand(0, $this->max - 1) * $this->packLength;
    }

    private function seekTo($position)
    {
        fseek($this->filePointer, $position);
    }

    private function getNumberAt($position)
    {
        $this->seekTo($position);
        return fread($this->filePointer, $this->packLength);
    }

    private function writeAt($position, $number)
    {
        $this->seekTo($position);
        fwrite($this->filePointer, $number);
    }

    private function randomFlip()
    {

        $firstPosition = $this->getRandomPosition();
        $secondPosition = $this->getRandomPosition();

        if ($firstPosition == $secondPosition) {
            return;
        }

        $firstNumber = $this->getNumberAt($firstPosition);
        $secondNumber = $this->getNumberAt($secondPosition);

        $this->writeAt($secondPosition, $firstNumber);
        $this->writeAt($firstPosition, $secondNumber);

    }

    public function shuffle($entropy = 1)
    {
        $this->initialize();

        $turns = $entropy * $this->max;
        for ($x = 0; $x < $turns; $x++) {
            $this->randomFlip();
        }
    }

    public function load($filePointer)
    {
        $this->filePointer = $filePointer;
        $this->initialized = true;
    }

    public function loadFile($fileName)
    {
        $this->load(fopen($fileName, 'r+'));
    }

    public function getIterator()
    {
        $this->initialize();

        if (!$this->initialized) {
            $this->initialize();
        }

        return new Iterator($this->max, $this->getPackLength(), $this->getPackFormat(), $this->filePointer);
    }

    public function copyToStream($dest)
    {
        $this->initialize();

        $this->seekTo(0);

        stream_copy_to_stream(
            $this->filePointer,
            $dest
        );
    }

    public function exportToFile($fileName)
    {
        $fp = fopen($fileName, 'w+');
        $this->copyToStream($fp);
        fclose($fp);
    }

}
