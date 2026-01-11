<?php

namespace Mediatis\Typo3CodingStandards\Php;

use Mediatis\CodingStandards\Php\CsFixerSetup;
use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use TYPO3\CodingStandards\CsFixerConfig;

class Typo3CsFixerSetup
{
    public static function create(): Config
    {
        $config = CsFixerConfig::create();
        CsFixerSetup::setup($config);

        $finder = $config->getFinder();
        if ($finder instanceof Finder) {
            $directories = ['Classes', 'Configuration', 'Tests'];
            foreach ($directories as $directory) {
                if (is_dir($directory)) {
                    $finder->in($directory);
                }
            }
        }

        return $config;
    }
}
