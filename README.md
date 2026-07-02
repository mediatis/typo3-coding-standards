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

Then generate the configuration files (`.php-cs-fixer.php`, `rector.php`, `phpstan.neon`, the GitLab/GitHub CI workflows, the `.ddev/` setup, the `composer ci` / `composer fix` scripts and the `Classes` / `Tests` folders) with the **`mediatis-typo3-coding-standards`** Claude Code skill.

In Claude Code, ask something like *"set up mediatis typo3 coding standards for this extension"*. The skill can:

- **scaffold** a brand-new extension from scratch,
- **add** the config to an existing extension (merging into `composer.json` without clobbering your keys), or
- **reset** the generated files (overwrite drifted CI/config), the `composer.json` scripts being merged rather than replaced.

The skill reads the supported PHP and TYPO3 versions from your `composer.json` and fills the CI matrix and rector targets accordingly — the same job the removed `mediatis-typo3-coding-standards-setup` binary used to do.

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
