<?php

declare(strict_types=1);

namespace ExampleVendor\ExampleExtension;

class Math
{
    public function add(int|float $x, int|float $y): float|int
    {
        return $x + $y;
    }
}
