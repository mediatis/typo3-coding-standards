<?php

declare(strict_types=1);

use Mediatis\Typo3CodingStandards\Php\Typo3RectorSetup;
use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void
{
    Typo3RectorSetup::setup($rectorConfig, __DIR__, TYPO3_VERSION_PLACEHOLDER, PhpVersion::PHP_VERSION_PLACEHOLDER);
};
