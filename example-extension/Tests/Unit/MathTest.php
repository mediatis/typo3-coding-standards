<?php

declare(strict_types=1);

namespace ExampleVendor\ExampleExtension\Tests\Unit;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use ExampleVendor\Typo3CodingStandards\Math;

class MathTest extends TestCase
{
    public function testAddIntegers()
    {
        $math = new Math();
        $result = $math->add(2, 3);
        $this->assertEquals(5, $result);
    }

    public function testAddFloats()
    {
        $math = new Math();
        $result = $math->add(2.5, 3.5);
        $this->assertEquals(6.0, $result);
    }

    public function testAddIntegerAndFloat()
    {
        $math = new Math();
        $result = $math->add(2, 3.5);
        $this->assertEquals(5.5, $result);
    }

    public function testAddFloatAndInteger()
    {
        $math = new Math();
        $result = $math->add(2.5, 3);
        $this->assertEquals(5.5, $result);
    }

    public function testAddNegativeNumbers()
    {
        $math = new Math();
        $result = $math->add(-2, -3);
        $this->assertEquals(-5, $result);
    }
}
