<?php

declare(strict_types=1);

namespace ExampleVendor\Typo3CodingStandards;

class Math
{
    public function add(int|float $x,int|float $y): float|int {
        return $x + $x;
    }
}