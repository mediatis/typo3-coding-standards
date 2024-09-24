<?php

namespace Mediatis\Typo3CodingStandards;

use Exception;
use Mediatis\CodingStandards\CodingStandardsSetup;

class Typo3CodingStandardsSetup extends CodingStandardsSetup
{
    public function setup(): void
    {
        parent::setup();
        $this->updateFile('Build/phpunit/FunctionalTests.xml');
        $this->updateFile('Build/phpunit/UnitTests.xml');
        $this->updateFile('Build/Scripts/runTests.sh');
    }

    public function reset(): void
    {
        parent::reset();
        $this->resetFile('Build/phpunit/FunctionalTests.xml');
        $this->resetFile('Build/phpunit/UnitTests.xml');
        $this->resetFile('Build/Scripts/runTests.sh');
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
        $versionConstraint = $this->getDependencyVersionConstraintsFromComposerData('typo3', 'major');
        if ($versionConstraint !== []) {
            $versionConstraint = reset($versionConstraint); // Only need the lowest supported version
            $sourceFilePath = 'rector-typo3-' . $versionConstraint . '.php';
            $this->updateFile($sourceFilePath, 'rector.php');
        } else {
            throw new Exception('Unable to set up rector due to version mismatch. Supported TYPO3 versions are: ' . implode(', ', $this->supportedPackageVersions['typo3']['versions']));
        }
    }
}
