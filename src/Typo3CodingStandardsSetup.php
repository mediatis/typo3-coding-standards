<?php

namespace Mediatis\Typo3CodingStandards;

use Exception;
use Mediatis\CodingStandards\CodingStandardsSetup;

class Typo3CodingStandardsSetup extends CodingStandardsSetup
{
    /**
     * Maximum PHP version supported per TYPO3 major version.
     *
     * Note: TYPO3 12 officially supports PHP 8.4, but the coding-standards
     * tooling (Rector/PHPStan) cannot run on PHP 8.4 with TYPO3 12's
     * dependency constraints. The ceiling of 8.3 reflects this practical
     * limitation, not TYPO3's own PHP support range.
     *
     * @var array<int,float>
     */
    protected const TYPO3_MAXIMUM_PHP_VERSION = [
        12 => 8.3,
        13 => 8.5,
        14 => 8.5,
    ];

    public function setup(): void
    {
        parent::setup();
        $phpVersions = $this->getDependencyVersionConstraintsFromComposerData('php', '');
        $packageName = $this->getPackageNameFromComposerData();
        $this->updateFile('.ddev/config.yaml',
            config: [
                'name' => str_replace('_', '-', $packageName),
                'php_version' => $phpVersions[0],
            ]
        );
        $this->updateFile('.ddev/php/custom-php.ini');
    }

    protected function setupCiPipeline(): void
    {
        parent::setupCiPipeline();
        $phpVersions = $this->getDependencyVersionConstraintsFromComposerData('php', outputType: 'float');
        $typo3Versions = $this->getDependencyVersionConstraintsFromComposerData('typo3', outputType: 'float');

        // GitLab CI: no native exclude support, so build separate matrix entries
        $gitlabMatrix = $this->buildGitlabMatrix($phpVersions, $typo3Versions);
        $this->updateFile('.gitlab-ci.yml',
            config: [
                'code-tests' => [
                    'parallel' => [
                        'matrix' => $gitlabMatrix,
                    ],
                ],
            ]
        );

        // GitHub Actions: supports exclude in the matrix
        $githubMatrix = $this->buildGithubMatrix($phpVersions, $typo3Versions);
        $this->updateFile('.github/workflows/ci.yml',
            config: [
                'jobs' => [
                    'code-quality' => [
                        'strategy' => [
                            'matrix' => $githubMatrix,
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Build a GitHub Actions matrix with exclude entries for incompatible combinations.
     *
     * @param float[] $phpVersions
     * @param float[] $typo3Versions
     *
     * @return array<string,mixed>
     */
    protected function buildGithubMatrix(array $phpVersions, array $typo3Versions): array
    {
        $matrix = [
            'php_version' => array_map(static fn (float $v): string => (string) $v, $phpVersions),
            'typo3_version' => array_map(static fn (float $v): string => (string) $v, $typo3Versions),
        ];

        $exclude = [];
        foreach ($typo3Versions as $typo3Version) {
            $maxPhp = static::TYPO3_MAXIMUM_PHP_VERSION[(int) $typo3Version] ?? null;
            if ($maxPhp === null) {
                continue;
            }

            foreach ($phpVersions as $phpVersion) {
                if ($phpVersion > $maxPhp) {
                    $exclude[] = [
                        'php_version' => (string) $phpVersion,
                        'typo3_version' => (string) $typo3Version,
                    ];
                }
            }
        }

        if ($exclude !== []) {
            $matrix['exclude'] = $exclude;
        }

        return $matrix;
    }

    /**
     * Build a GitLab CI matrix with separate entries to avoid incompatible combinations.
     *
     * @param float[] $phpVersions
     * @param float[] $typo3Versions
     *
     * @return array<int,array<string,string[]>>
     */
    protected function buildGitlabMatrix(array $phpVersions, array $typo3Versions): array
    {
        // Group TYPO3 versions by their compatible PHP versions
        $groups = [];
        foreach ($typo3Versions as $typo3Version) {
            $maxPhp = static::TYPO3_MAXIMUM_PHP_VERSION[(int) $typo3Version] ?? PHP_FLOAT_MAX;
            $compatiblePhpVersions = array_values(array_filter(
                $phpVersions,
                static fn (float $phpVersion): bool => $phpVersion <= $maxPhp,
            ));

            $key = implode(',', $compatiblePhpVersions);
            $groups[$key]['php_versions'] = $compatiblePhpVersions;
            $groups[$key]['typo3_versions'][] = $typo3Version;
        }

        // Build matrix entries
        $matrix = [];
        foreach ($groups as $group) {
            $matrix[] = [
                'php_version' => array_map(static fn (float $v): string => (string) $v, $group['php_versions']),
                'typo3_version' => array_map(static fn (float $v): string => (string) $v, $group['typo3_versions']),
            ];
        }

        return $matrix;
    }

    public function reset(): void
    {
        parent::reset();
        $this->resetFile('.ddev/config.yaml');
        $this->resetFile('.ddev/php/custom-php.ini');
    }

    /**
     * @param array<string,mixed> $data
     *
     * @throws Exception
     */
    protected function getExtensionKeyFromComposerData(array $data): string
    {
        if (!isset($data['extra']['typo3/cms']['extension-key'])) {
            throw new Exception('No extension key found in composer.json');
        }

        return $data['extra']['typo3/cms']['extension-key'];
    }

    protected function updateDataComposerJson(array $sourceData, array &$targetData, array $config): void
    {
        parent::updateDataComposerJson($sourceData, $targetData, $config);

        $sourceExtensionKey = $this->getExtensionKeyFromComposerData($sourceData);
        $targetExtensionKey = $this->getExtensionKeyFromComposerData($targetData);
        foreach ($targetData['scripts']['link-extension'] ?? [] as $index => $value) {
            $targetData['scripts']['link-extension'][$index] = str_replace($sourceExtensionKey, $targetExtensionKey, (string)$value);
        }
    }

    protected function setupRectorConfig(): void
    {
        $phpVersions = $this->getDependencyVersionConstraintsFromComposerData('php', '');
        $phpVersion = match ($phpVersions[0]) {
            8.2 => 'PHP_82',
            8.3 => 'PHP_83',
            8.4 => 'PHP_84',
            8.5 => 'PHP_85',
            default => throw new Exception('Unable to set up rector due to version mismatch. Supported PHP versions are: ' . implode(', ', $this->supportedPackageVersions['php']['versions'])),
        };
        $typo3Versions = $this->getDependencyVersionConstraintsFromComposerData('typo3', 'major');
        if ($typo3Versions !== []) {
            $this->updateFile('rector.php', config: [
                'TYPO3_VERSION_PLACEHOLDER' => $typo3Versions[0],
                'PHP_VERSION_PLACEHOLDER' => $phpVersion,
            ]);
        } else {
            throw new Exception('Unable to set up rector due to version mismatch. Supported TYPO3 versions are: ' . implode(', ', $this->supportedPackageVersions['typo3']['versions']));
        }
    }
}
