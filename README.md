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
./.Build/bin/mediatis-typo3-coding-standards-setup
```

Start ddev in the extension folder

```
ddev start
```

## Usage - Check

Run all checks:

```
ddev composer ci
```

Run group checks:

```
# run all code quality checks
ddev composer ci:static

# all php tests and code quality checks
ddev composer ci:php
ddev composer ci:composer
ddev composer ci:yaml
ddev composer ci:json
```

Run specific checks:

```
ddev composer ci:composer:normalize
ddev composer ci:composer:psr-verify
ddev composer ci:composer:validate
ddev composer ci:php:lint
ddev composer ci:php:rector
ddev composer ci:php:cs-fixer
ddev composer ci:php:stan
ddev composer ci:php:tests:unit
ddev composer ci:php:tests:functional
ddev composer ci:yaml:lint
ddev composer ci:json:lint
```

## Usage - Fix

Run all fixes:

```
ddev composer fix
```

Run group fixes:

```
ddev composer fix:composer
ddev composer fix:php
```

Run specific fixes:

```
ddev composer fix:php:rector
ddev composer fix:php:cs
ddev composer fix:composer:normalize
```
