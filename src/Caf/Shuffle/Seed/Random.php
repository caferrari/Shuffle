<?php

namespace Caf\Shuffle\Seed;

class Random extends Seedable
{

    protected $min;

    public function __construct($min)
    {
        $this->min = $min;
    }

    public function getNext()
    {
        return mt_rand($this->min, $this->max);
    }

}