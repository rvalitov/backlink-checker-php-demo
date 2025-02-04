# Backlink Checker PHP Demo

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
