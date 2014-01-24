<?php

namespace Caf\Shuffle\Seed;

class Sequential extends Seedable
{

    private $number = 0;

    public function getNext()
    {
        return $this->number++;
    }

}