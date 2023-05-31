# About 
This is a demo project to demonstrate how the [Backlink Checker PHP library](https://github.com/rvalitov/backlink-checker-php) works.

#### SYNOPSIS

php **cli_test.php** -u *URL* -p *PATTERN* [-m *MODE*]

#### DESCRIPTION
Script is used to check if a specified *URL* contains a backlink to a website defined by the *PATTERN* which is a [regular expression](https://en.wikipedia.org/wiki/Regular_expression). Optional parameter *MODE* defines what engine will be used for website scraping and can be one of the following:

- `javascript` (default) - Chromium headless mode is used with JavaScript support
- `simple` - simple parsing of HTML, without JavaScript support

The script is useful for SEO experts when you need to validate that your backlinks still exist on the web pages where you set them or bought them.

If `javascript` mode is used, then the script will save a screenshot of the browser's viewport and save it in JPEG format in the `screenshots` folder.

# Usage Example
Input and output:

```
php cli_test.php -u https://dubaidance.com/ -p "@^https://(www\.)?dubaidance\.com.*@"
Using mode: javascript
Found 1 backlinks
Found <a> src=https://dubaidance.com/ anchor=go now
All operations complete
```
