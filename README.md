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
composer run-script ci
```

Run group checks:

```
# run all code quality checks
composer run-script ci:static

# all php tests and code quality checks
composer run-script ci:php
```

Run specific checks:

```
composer run-script ci:composer:normalize
composer run-script ci:composer:psr-verify
composer run-script ci:php:rector
composer run-script ci:php:cs-fixer
composer run-script ci:php:stan
```

## Usage - Fix

Run group fixes:

```
composer run-script fix:php
```

Run specific fixes:

```
composer run-script fix:php:rector
composer run-script fix:php:cs
composer run-script fix:composer:normalize
```
