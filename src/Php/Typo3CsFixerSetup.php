<?php

namespace Mediatis\Typo3CodingStandards\Php;

use Mediatis\CodingStandards\Php\CsFixerSetup;
use PhpCsFixer\Config;
use TYPO3\CodingStandards\CsFixerConfig;

class Typo3CsFixerSetup
{
    public static function create(): Config
    {
        $config = CsFixerConfig::create();
        CsFixerSetup::setup($config);

        $config->getFinder()->in('Classes')->in('Configuration')->in('Tests');

        return $config;
    }
}
