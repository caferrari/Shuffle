<?php

namespace Caf\Shuffle\Seed;

abstract class Seedable
{

    protected $max;

    abstract function getNext();

    public function setMax($max)
    {
        $this->max = $max;
    }

}