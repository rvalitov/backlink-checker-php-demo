# Backlink Checker PHP Demo

![PHP version icon](https://img.shields.io/static/v1?label=PHP&message=8.3&color=blue)
![Platform icon](https://img.shields.io/badge/Platform-Windows%2C%20Linux%2C%20Mac-blue)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/181ac06fbd2b471496c729347f30f001)](https://app.codacy.com/gh/rvalitov/backlink-checker-php-demo/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)
[![Codacy Badge](https://app.codacy.com/project/badge/Coverage/181ac06fbd2b471496c729347f30f001)](https://app.codacy.com/gh/rvalitov/backlink-checker-php-demo/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_coverage)
[![Code Smells](https://sonarcloud.io/api/project_badges/measure?project=rvalitov_backlink-checker-php-demo&metric=code_smells)](https://sonarcloud.io/summary/new_code?id=rvalitov_backlink-checker-php-demo)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=rvalitov_backlink-checker-php-demo&metric=sqale_rating)](https://sonarcloud.io/summary/new_code?id=rvalitov_backlink-checker-php-demo)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=rvalitov_backlink-checker-php-demo&metric=security_rating)](https://sonarcloud.io/summary/new_code?id=rvalitov_backlink-checker-php-demo)
[![Bugs](https://sonarcloud.io/api/project_badges/measure?project=rvalitov_backlink-checker-php-demo&metric=bugs)](https://sonarcloud.io/summary/new_code?id=rvalitov_backlink-checker-php-demo)
[![Vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=rvalitov_backlink-checker-php-demo&metric=vulnerabilities)](https://sonarcloud.io/summary/new_code?id=rvalitov_backlink-checker-php-demo)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=rvalitov_backlink-checker-php-demo&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=rvalitov_backlink-checker-php-demo)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=rvalitov_backlink-checker-php-demo&metric=sqale_index)](https://sonarcloud.io/summary/new_code?id=rvalitov_backlink-checker-php-demo)
[![Tests](https://github.com/rvalitov/backlink-checker-php-demo/actions/workflows/tests.yml/badge.svg?branch=master)](https://github.com/rvalitov/backlink-checker-php-demo/actions/workflows/tests.yml)
![GitHub License](https://img.shields.io/github/license/rvalitov/backlink-checker-php-demo?color=blue)

- This is a demo project to demonstrate how
  the [Backlink Checker PHP library](https://github.com/rvalitov/backlink-checker-php) works.
- The script is useful for SEO experts to validate your backlink assets.
- It checks that the backlinks are present or not on the defined web page.
- You get the web page from your backlink collection (bought at market or got by other means).

## Installation

1. Clone the repository
2. Install dependencies with Composer `composer install --no-dev`
3. Install dependencies with NPM `npm ci --omit=dev`

## Synopsis

The executable script is in `src` folder.
Script usage:

```console
php cli_test.php -u URL -p PATTERN [-m MODE]
```

Arguments:

- `URL` (required) — URL to check for backlinks
- `PATTERN` (required) — a regular expression pattern that defines a backlink to search for
- `MODE` (optional) — scraping mode. Possible values:
  - `javascript` (default) - Chromium headless mode is used with JavaScript support
  - `simple` - simple parsing of HTML, without JavaScript support

If `javascript` mode is used then the script takes a screenshot of the web page
and saves it in JPEG format in the `screenshots` folder.

## Usage Example

Input and output:

```console
php cli_test.php -u https://dubaidance.com/ -p "@^https://(www\.)?dubaidance\.com.*@"
Using mode: javascript
Found 1 backlinks
Found <a> src=https://dubaidance.com/ anchor=go now
All operations complete
```

## References

- [Backlink Checker PHP library](https://github.com/rvalitov/backlink-checker-php)
- [Regular expression pattern](https://www.php.net/manual/en/reference.pcre.pattern.syntax.php)
- [Regular expression playground](https://regex101.com/)
