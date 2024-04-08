<?php

declare(strict_types=1);

namespace ExampleVendor\ExampleExtension;

class Math
{
    /**
     * @template T of int|float
     *
     * @param T $x
     * @param T $y
     *
     * @return T
     */
    public function add(int|float $x, int|float $y): float|int
    {
        return $x + $y;
    }
}
