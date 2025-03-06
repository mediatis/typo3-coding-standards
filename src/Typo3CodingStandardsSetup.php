<?php

namespace Mediatis\Typo3CodingStandards;

use Exception;
use Mediatis\CodingStandards\CodingStandardsSetup;

class Typo3CodingStandardsSetup extends CodingStandardsSetup
{
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
        $matrix = [];
        foreach (array_keys($this->supportedPackageVersions) as $package) {
            $matrix[$package . '_version'] = $this->getDependencyVersionConstraintsFromComposerData($package, outputType: 'string');
        }

        $this->updateFile('.gitlab-ci.yml',
            config: [
                'code-tests' => [
                    'parallel' => [
                        'matrix' => [
                            $matrix,
                        ],
                    ],
                ],
            ]
        );
        $matrix = [];
        foreach (array_keys($this->supportedPackageVersions) as $package) {
            $matrix[$package . '_version'] = $this->getDependencyVersionConstraintsFromComposerData($package, outputType: 'string');
        }

        $this->updateFile('.github/workflows/ci.yml',
            config: [
                'jobs' => [
                    'code-tests' => [
                        'strategy' => [
                            'matrix' => $matrix,
                        ],
                    ],
                ],
            ]
        );
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
