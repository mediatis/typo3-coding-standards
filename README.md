# TYPO3 Code Quality Package

## Installation

Make sure that you removed old code quality and pipeline configuration files or folders, e.g. `rector.php`, `.php-cs-fixer.php`, `.phpstan`.

Make sure, your `composer.json` does not have any dev-requirements on explicit code-quality packages (like `phpunit/phpunit`, `rector/rector`, `typo3/coding-standards` and so on).

Make sure your `.gitignore` file includes the folder `.Build` and the file `composer.lock`.

```
.Build
composer.lock
```

Install the TYPO3 coding-standards package.

```
composer require --dev --with-all-dependencies mediatis/typo3-coding-standards
```

Run the kickstart script to install configuration files. Pass the lowest TYPO3 major version number that your extension supports.

```
./.Build/bin/mediatis-typo3-coding-standards-setup 11|12
```

## Usage - Check

Run all checks:

```
composer ci
```

Run group checks:

```
# run all code quality checks
composer ci:static

# all php tests and code quality checks
composer ci:php
composer ci:composer
composer ci:yaml
composer ci:json
```

Run specific checks:

```
composer ci:composer:normalize
composer ci:composer:psr-verify
composer ci:composer:validate
composer ci:php:lint
composer ci:php:rector
composer ci:php:cs-fixer
composer ci:php:stan
composer ci:php:tests:unit
composer ci:php:tests:functional
```

## Usage - Fix

Run all fixes:

```
composer fix
```

Run group fixes:

```
composer fix:composer
composer fix:php
```

Run specific fixes:

```
composer fix:php:rector
composer fix:php:cs
composer fix:composer:normalize
```
