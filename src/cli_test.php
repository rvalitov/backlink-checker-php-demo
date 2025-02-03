<?php

/**
 * CLI script to check the backlinks on a page.
 * This is a demo of using the BacklinkChecker library.
 * See https://github.com/rvalitov/backlink-checker-php
 * @author Ramil Valitov
 * @license GPL-3.0
 */

namespace Valitov\BacklinkCheckerDemo;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/base.php";

use Valitov\BacklinkChecker;

if (php_sapi_name() !== 'cli') {
    Base::endProgram("This script can run in CLI mode only", \RuntimeException::class);
}

$shortOpts = "u:p:m:";
$options = getopt($shortOpts);
if ($options === false || !isset($options["u"]) || !isset($options["p"])) {
    Base::endProgram("Missing required arguments: u - for URL and p for RegExp pattern", \InvalidArgumentException::class);
}

$url = trim($options["u"]);

// Check that the URL uses the HTTP or HTTPS protocol
if (@preg_match('/^https?:\/\//', $url) !== 1) {
    Base::endProgram("HTTP or HTTPS protocol is required to be specified", \InvalidArgumentException::class);
}

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    Base::endProgram("Invalid URL", \InvalidArgumentException::class);
}

$pattern = trim($options["p"]);
if (@preg_match($pattern, '') === false) {
    Base::endProgram("Failed to validate RegExp pattern. Does it contain a syntax error?", \InvalidArgumentException::class);
}

if (!isset($options["m"]) || !is_string($options["m"]) || $options["m"] === "") {
    $options["m"] = "javascript";
}

$parameter = trim(strtolower($options["m"]));
switch ($parameter) {
    case "":
    case "javascript":
        $checker = new BacklinkChecker\ChromeBacklinkChecker();
        break;
    case "simple":
        $checker = new BacklinkChecker\SimpleBacklinkChecker();
        break;
    default:
        Base::endProgram("Invalid value for parameter mode: \"$parameter\"", \InvalidArgumentException::class);
}

echo "Using mode: $parameter" . PHP_EOL;

if (!file_exists(Base::SCREENSHOTS_DIR)) {
    if (!@mkdir(Base::SCREENSHOTS_DIR)) {
        Base::endProgram("Failed to create directory for screenshots", \RuntimeException::class);
    }
} else {
    if (!is_dir(Base::SCREENSHOTS_DIR)) {
        Base::endProgram("Screenshots directory is not a directory. Please remove the file with the same name.", \RuntimeException::class);
    }
}

try {
    $result = $checker->getBacklinks($url, $pattern, true, false, true);
} catch (\Exception $e) {
    Base::endProgramException($e);
}
$response = $result->getResponse();
$screenshot = $response->getScreenshot();
if ($screenshot) {
    $file_name = preg_replace('/[^a-z0-9]+/', '-', strtolower(html_entity_decode($url)));
    file_put_contents(Base::SCREENSHOTS_DIR . "/" . $file_name . ".jpg", $screenshot);
}
if (!$response->isSuccess()) {
    Base::endProgram("Failed to retrieve the page content. Error code: " . $response->getStatusCode(), \RuntimeException::class);
}

$links = $result->getBacklinks();
echo "Found " . sizeof($links) . " backlinks" . PHP_EOL;

foreach ($links as $link) {
    echo "Found <" . $link->getTag() . "> src=" . $link->getBacklink() . " anchor=" . $link->getAnchor() . PHP_EOL;
}
echo "All operations complete" . PHP_EOL;
