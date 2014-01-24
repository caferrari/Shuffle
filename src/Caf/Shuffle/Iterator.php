<?php

namespace Caf\Shuffle;

use \RuntimeException,
    \OutOfRangeException;

class Iterator implements \Countable, \ArrayAccess, \SeekableIterator
{

    private $max;
    private $packLength;
    private $packFormat;
    private $filePointer;

    private $position = 0;

    public function __construct($max, $packLength, $packFormat, $filePointer)
    {
        $this->max = $max;
        $this->packLength = $packLength;
        $this->packFormat = $packFormat;
        $this->filePointer = $filePointer;
    }

    public function count()
    {
        return $this->max;
    }

    private function moveToOffset($offset)
    {
        $tell = ftell($this->filePointer);
        $seek = ($offset * $this->packLength) - $tell;
        fseek($this->filePointer, $seek, SEEK_CUR);
    }

    private function unpack($package)
    {
        $data = unpack($this->packFormat, $package);
        return reset($data);
    }

    private function readCurrentValue()
    {
        $this->moveToOffset($this->position);
        return $this->unpack(fread($this->filePointer, $this->packLength));
    }

    public function offsetExists($offset)
    {
        return $offset < $this->max && $offset >= 0;
    }

    public function offsetGet($offset)
    {
        $this->moveToOffset($offset);
        return $this->readCurrentValue();
    }

    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('This data is read only');
    }

    public function offsetUnset($offset)
    {
        throw new RuntimeException('This data is read only');
    }

    public function seek($position)
    {
        if ($this->offsetExists($position)) {
            $this->position = $position;
        }

        throw new OutOfRangeException('Invalid index');
    }

    public function current()
    {
        return $this->readCurrentValue();
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        $this->position++;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function valid()
    {
        return $this->offsetExists($this->position);
    }

}