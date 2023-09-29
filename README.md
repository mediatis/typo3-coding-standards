# Code Quality Package

## Installation

```
composer require --dev mediatis/typo3-coding-standards
```

## rector.php

```
<?php

use Mediatis\Typo3CodingStandards\Php\Typo3RectorSetup;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void
{
    Typo3RectorSetup::setup($rectorConfig, __DIR__);
};
```

## .php-cs-fixer.php

```
<?php

return \Mediatis\Typo3CodingStandards\Php\Typo3CsFixerSetup::create();
```

## phpstan.neon

```
includes:
	- vendor/mediatis/typo3-coding-standards/phpstan.neon
```

## composer.json

```
"scripts": {
    "post-autoload-dump": [
        "@link-extension"
    ],
    "ci": [
        "@ci:static"
    ],
    "ci:composer:normalize": "@composer normalize --no-check-lock --dry-run",
    "ci:composer:psr-verify": "@composer dumpautoload --optimize --strict-psr",
    "ci:coverage": [
        "@ci:coverage:unit",
        "@ci:coverage:functional"
    ],
    "ci:coverage:functional": [
        "@coverage:create-directories",
        ".Build/bin/phpunit -c .Build/vendor/typo3/testing-framework/Resources/Core/Build/FunctionalTests.xml --whitelist Classes --coverage-php=.Build/coverage/functional.cov Tests/Functional"
    ],
    "ci:coverage:merge": [
        "@coverage:create-directories",
        "@php tools/phpcov merge --clover=./.Build/logs/clover.xml ./.Build/coverage/"
    ],
    "ci:coverage:unit": [
        "@coverage:create-directories",
        ".Build/bin/phpunit -c .Build/vendor/typo3/testing-framework/Resources/Core/Build/UnitTests.xml --whitelist Classes --coverage-php=.Build/coverage/unit.cov Tests/Unit"
    ],
    "ci:dynamic": [
        "@ci:tests"
    ],
    "ci:json:lint": "find . ! -path '*.Build/*' ! -path '*node_modules/*' -name '*.json' | xargs -r php .Build/bin/jsonlint -q",
    "ci:php": [
        "@ci:php:rector",
        "@ci:php:cs-fixer",
        "@ci:php:lint",
        "@ci:php:stan"
    ],
    "ci:php:cs-fixer": "php-cs-fixer fix --config .php-cs-fixer.php -v --dry-run --using-cache no --diff",
    "ci:php:lint": "find .*.php *.php Classes Configuration Tests -name '*.php' -print0 | xargs -r -0 -n 1 -P 4 php -l",
    "ci:php:rector": "rector --dry-run",
    "ci:php:stan": "phpstan --no-progress",
    "ci:static": [
        "@ci:composer:normalize",
        "@ci:json:lint",
        "@ci:php:cs-fixer",
        "@ci:php:lint",
        "@ci:php:stan",
        "@ci:yaml:lint"
    ],
    "ci:tests": [
        "@ci:tests:unit",
        "@ci:tests:functional"
    ],
    "ci:tests:functional": "find 'Tests/Functional' -wholename '*Test.php' | parallel --gnu 'echo; echo \"Running functional test suite {}\"; .Build/bin/phpunit -c .Build/vendor/typo3/testing-framework/Resources/Core/Build/FunctionalTests.xml {}';",
    "ci:tests:unit": ".Build/bin/phpunit -c .Build/vendor/typo3/testing-framework/Resources/Core/Build/UnitTests.xml Tests/Unit",
    "ci:yaml:lint": "find . ! -path '*.Build/*' ! -path '*node_modules/*' -regextype egrep -regex '.*.ya?ml$' | xargs -r php ./.Build/bin/yaml-lint",
    "coverage:create-directories": "mkdir -p .Build/logs .Build/coverage",
    "docs:generate": [
        "@docs:generate:pullimage",
        "docker run --rm ghcr.io/t3docs/render-documentation show-shell-commands > tempfile.sh; echo 'dockrun_t3rd makehtml' >> tempfile.sh; bash tempfile.sh; rm tempfile.sh"
    ],
    "docs:generate:pullimage": [
        "docker pull ghcr.io/t3docs/render-documentation",
        "docker tag ghcr.io/t3docs/render-documentation t3docs/render-documentation"
    ],
    "fix:composer:normalize": "@composer normalize --no-check-lock",
    "fix:php": [
        "@fix:php:rector",
        "@fix:php:cs"
    ],
    "fix:php:cs": "php-cs-fixer fix --config .php-cs-fixer.php",
    "fix:php:rector": "rector",
    "link-extension": [
        "@php -r 'is_dir($extFolder=__DIR__.\"/.Build/Web/typo3conf/ext/\") || mkdir($extFolder, 0777, true);'",
        "@php -r 'file_exists($extFolder=__DIR__.\"/.Build/Web/typo3conf/ext/example_extension\") || symlink(__DIR__,$extFolder);'"
    ],
    "phpstan:baseline": ".Build/bin/phpstan  --generate-baseline=.phpstan/phpstan-baseline.neon",
    "prepare-release": [
        "rm .gitignore",
        "rm -rf .Build",
        "rm -rf .ddev",
        "rm -rf .github",
        "rm -rf .gitlab",
        "rm -rf Build",
        "rm -rf Tests",
        "rm -rf tools",
        "rm .editorconfig",
        "rm .gitattributes",
        "rm .php-cs-fixer.php",
        "rm .eslintignore",
        "rm .eslintrc.json",
        "rm .prettierrc.js",
        "rm package.json",
        "rm stylelint.config.js",
        "rm phive.xml",
        "rm phpstan-baseline.neon",
        "rm phpstan.neon",
        "rm phpcs.xml"
    ]
},
"scripts-descriptions": {
    "ci": "Runs all dynamic and static code checks.",
    "ci:composer:normalize": "Checks the composer.json.",
    "ci:composer:psr-verify": "Verifies PSR-4 namespace correctness.",
    "ci:coverage:functional": "Generates the code coverage report for functional tests.",
    "ci:coverage:merge": "Merges the code coverage reports for unit and functional tests.",
    "ci:coverage:unit": "Generates the code coverage report for unit tests.",
    "ci:dynamic": "Runs all PHPUnit tests (unit and functional).",
    "ci:json:lint": "Lints the JSON files.",
    "ci:php": "Runs all static checks for the PHP files.",
    "ci:php:cs-fixer": "Checks the code style with the PHP Coding Standards Fixer (PHP-CS-Fixer).",
    "ci:php:lint": "Lints the PHP files for syntax errors.",
    "ci:php:rector": "Checks the code style with the TYPO3 rector (typo3-rector).",
    "ci:php:stan": "Checks the PHP types using PHPStan.",
    "ci:static": "Runs all static code checks (syntax, style, types).",
    "ci:tests": "Runs all PHPUnit tests (unit and functional).",
    "ci:tests:functional": "Runs the functional tests.",
    "ci:tests:unit": "Runs the unit tests.",
    "ci:yaml:lint": "Lints the YAML files.",
    "coverage:create-directories": "Creates the directories needed for recording and merging the code coverage reports.",
    "docs:generate": "Renders the extension ReST documentation.",
    "fix:composer:normalize": "Normalizes composer.json file content.",
    "fix:php": "Runs all fixers for the PHP code.",
    "fix:php:cs": "Fixes the code style with PHP-CS-Fixer.",
    "phpstan:baseline": "Updates the PHPStan baseline file to match the code.",
    "prepare-release": "Removes development-only files in preparation of a TER release."
}
```

## .github/workflows/ci.yml

```
---
# This GitHub Actions workflow uses the same development tools that are also installed locally
# via Composer or PHIVE and calls them using the Composer scripts.
name: CI with Composer scripts
on:
  push:
    branches: [main]
  pull_request:
    branches: [main]
permissions:
  contents: read
jobs:
  php-lint:
    name: 'PHP linter'
    runs-on: ubuntu-22.04
    steps:
      - name: 'Checkout'
        uses: actions/checkout@v4
      - name: 'Install PHP'
        uses: shivammathur/setup-php@v2
        with:
          php-version: '${{ matrix.php-version }}'
          coverage: none
          tools: composer:v2.4
      - name: 'Show the Composer configuration'
        run: 'composer config --global --list'
      - name: 'Run PHP lint'
        run: 'composer ci:php:lint'
    strategy:
      fail-fast: false
      matrix:
        php-version:
          - '8.1'
          - '8.2'
  code-quality:
    name: 'Code quality checks'
    runs-on: ubuntu-22.04
    steps:
      - name: 'Checkout'
        uses: actions/checkout@v4
      - name: 'Install PHP'
        uses: shivammathur/setup-php@v2
        with:
          php-version: '${{ matrix.php-version }}'
          coverage: none
          tools: composer:v2.4
      - name: 'Show Composer version'
        run: 'composer --version'
      - name: 'Show the Composer configuration'
        run: 'composer config --global --list'
      - name: 'Cache dependencies installed with composer'
        uses: actions/cache@v3
        with:
          key: "php${{ matrix.php-version }}-composer-${{ hashFiles('**/composer.json') }}"
          path: ~/.cache/composer
          restore-keys: "php${{ matrix.php-version }}-composer-\n"
      - name: 'Install Composer dependencies'
        run: 'composer install --no-progress'
      - name: 'Run command'
        run: 'composer ci:${{ matrix.command }}'
    strategy:
      fail-fast: false
      matrix:
        command:
          - 'composer:normalize'
          - 'composer:psr-verify'
          - 'json:lint'
          - 'php:rector'
          - 'php:cs-fixer'
          - 'php:stan'
        php-version:
          - '8.1'
          - '8.2'
```
