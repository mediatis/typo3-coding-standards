<?php

declare(strict_types=1);

namespace ExampleVendor\ExampleExtension\Tests\Functional;

use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class MathUtilityTest extends FunctionalTestCase
{
    public function testAddIntegers(): void
    {
        $result = MathUtility::calculateWithParentheses('2 + 3');
        self::assertEquals(5, $result);
    }
}
