<?php

namespace Mediatis\Typo3CodingStandards\Php;

use Exception;
use Mediatis\CodingStandards\Php\RectorSetup;
use Rector\Config\RectorConfig;
use Rector\PostRector\Rector\NameImportingPostRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;
use Rector\ValueObject\PhpVersion;
use Ssch\TYPO3Rector\CodeQuality\General\ConvertImplicitVariablesToExplicitGlobalsRector;
use Ssch\TYPO3Rector\CodeQuality\General\ExtEmConfRector;
use Ssch\TYPO3Rector\Configuration\Typo3Option;
use Ssch\TYPO3Rector\Set\Typo3LevelSetList;
use Ssch\TYPO3Rector\Set\Typo3SetList;
use Ssch\TYPO3Rector\TYPO313\v4\MigratePluginContentElementAndPluginSubtypesRector;

class Typo3RectorSetup extends RectorSetup
{
    protected static int $phpVersion;

    protected static int $typo3Version;

    /**
     * @return string[]
     */
    protected static function paths(string $packagePath): array
    {
        return [
            $packagePath, // check the whole extension package, not just Classes and Tests
        ];
    }

    protected static function sets(): array
    {
        $sets = parent::sets();
        array_push($sets, ...[
            match (static::$typo3Version) {
                12 => Typo3LevelSetList::UP_TO_TYPO3_12,
                13 => Typo3LevelSetList::UP_TO_TYPO3_13,
                default => throw new Exception(sprintf('unkonwn typo3 version "%s"', static::$typo3Version)),
            },
            Typo3SetList::CODE_QUALITY,
            Typo3SetList::GENERAL,
        ]);

        return array_unique($sets);
    }

    protected static function skip(string $packagePath): array
    {
        $criteria = parent::skip($packagePath);
        // If you use importNames(), you should consider excluding some TYPO3 files.
        $typo3Criteria = [
            // @see https://github.com/sabbelasichon/typo3-rector/issues/2536
            $packagePath . '/**/Configuration/ExtensionBuilder/*',
            // We skip those directories on purpose as there might be node_modules or similar
            // that include typescript which would result in false positive processing
            $packagePath . '/**/Resources/**/node_modules/*',
            $packagePath . '/**/Resources/**/NodeModules/*',
            $packagePath . '/**/Resources/**/BowerComponents/*',
            $packagePath . '/**/Resources/**/bower_components/*',
            $packagePath . '/**/Resources/**/build/*',
            $packagePath . '/**/node_modules/*',
            $packagePath . '/vendor/*',
            $packagePath . '/Build/*',
            $packagePath . '/public/*',
            $packagePath . '/.github/*',
            $packagePath . '/.Build/*',
            NameImportingPostRector::class => [
                'ext_localconf.php',
                'ext_tables.php',
                'ClassAliasMap.php',
                $packagePath . '/**/Configuration/*.php',
                $packagePath . '/**/Configuration/**/*.php',
            ],
            MigratePluginContentElementAndPluginSubtypesRector::class,
        ];
        foreach ($typo3Criteria as $key => $value) {
            if (is_numeric($key)) {
                $criteria[] = $value;
            } else {
                $criteria[$key] = $value;
            }
        }

        return $criteria;
    }

    public static function setup(RectorConfig $rectorConfig, string $packagePath, int $typo3Version = 12, int $phpVersion = PhpVersion::PHP_82): void
    {
        static::$typo3Version = $typo3Version;
        static::$phpVersion = $phpVersion;
        parent::setup($rectorConfig, $packagePath, $phpVersion);

        // If you want to override the number of spaces for your typoscript files you can define it here, the default value is 4
        // $parameters = $rectorConfig->parameters();
        // $parameters->set(Typo3Option::TYPOSCRIPT_INDENT_SIZE, 2);

        // In order to have a better analysis from phpstan we teach it here some more things
        $rectorConfig->phpstanConfig(Typo3Option::PHPSTAN_FOR_RECTOR_PATH);

        // FQN classes are not imported by default. If you don't do it manually after every Rector run, enable it by:
        $rectorConfig->importNames();

        // Disable parallel otherwise non php file processing is not working i.e. typoscript
        $rectorConfig->disableParallel();

        // this will not import root namespace classes, like \DateTime or \Exception
        $rectorConfig->importShortClasses(false);
        // Define your target version which you want to support
        /** @phpstan-ignore-next-line  */
        $rectorConfig->phpVersion($phpVersion);

        // When you use rector there are rules that require some more actions like creating UpgradeWizards for outdated TCA types.
        // To fully support you we added some warnings. So watch out for them.

        // If you have trouble that rector cannot run because some TYPO3 constants are not defined add an additional constants file
        // @see https://github.com/sabbelasichon/typo3-rector/blob/master/typo3.constants.php
        // @see https://github.com/rectorphp/rector/blob/main/docs/static_reflection_and_autoload.md#include-files
        // $parameters->set(Option::BOOTSTRAP_FILES, [
        //    $packagePath . '/typo3.constants.php'
        // ]);

        // register a single rule
        // $rectorConfig->rule(\Ssch\TYPO3Rector\Rector\v9\v0\InjectAnnotationRector::class);

        /**
         * Useful rule from RectorPHP itself to transform i.e. GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')
         * to GeneralUtility::makeInstance(\TYPO3\CMS\Core\Log\LogManager::class) calls.
         * But be warned, sometimes it produces false positives (edge cases), so watch out
         */
        // $rectorConfig->rule(\Rector\Php55\Rector\String_\StringClassNameToClassConstantRector::class);

        // Optional non-php file functionalities:
        // @see https://github.com/sabbelasichon/typo3-rector/blob/main/docs/beyond_php_file_processors.md

        // Rewrite your extbase persistence class mapping from typoscript into php according to official docs.
        // This processor will create a summarized file with all the typoscript rewrites combined into a single file.
        /* $rectorConfig->ruleWithConfiguration(\Ssch\TYPO3Rector\FileProcessor\TypoScript\Rector\v10\v0\ExtbasePersistenceTypoScriptRector::class, [
            \Ssch\TYPO3Rector\FileProcessor\TypoScript\Rector\v10\v0\ExtbasePersistenceTypoScriptRector::FILENAME => $packagePath . '/packages/acme_demo/Configuration/Extbase/Persistence/Classes.php',
        ]); */
        // Add some general TYPO3 rules
        $rectorConfig->rules([
            ConvertImplicitVariablesToExplicitGlobalsRector::class,
            AddVoidReturnTypeWhereNoReturnRector::class,
            ConvertImplicitVariablesToExplicitGlobalsRector::class,
        ]);

        $rectorConfig->ruleWithConfiguration(ExtEmConfRector::class, [
            ExtEmConfRector::ADDITIONAL_VALUES_TO_BE_REMOVED => [],
        ]);
    }
}
