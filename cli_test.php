<?php

require __DIR__ . "/vendor/autoload.php";

define("SCREENSHOTS_DIR", __DIR__ . "/screenshots");

use Valitov\BacklinkChecker;

if (php_sapi_name() !== 'cli') {
    die("This script can run in CLI mode only");
}

$climate = new \League\CLImate\CLImate;

$shortOpts = "u:p:m::";

$options = getopt($shortOpts);
if ($options === false || !isset($options["u"]) || !isset($options["p"])) {
    $climate->error("Missing required arguments: u - for URL and p for RegExp pattern");
    exit(1);
}

$url = $options["u"];
$pattern = $options["p"];

if (!isset($options["m"]))
    $options["m"] = "javascript";

$parameter = strtolower($options["m"]);
switch ($parameter) {
    case "javascript":
        $checker = new BacklinkChecker\ChromeBacklinkChecker();
        break;
    case "simple":
        $checker = new BacklinkChecker\SimpleBacklinkChecker();
        break;
    default:
        $climate->error("Invalid value for parameter mode");
        exit(1);
}

$climate->cyan("Using mode: " . $parameter);

if (!file_exists(SCREENSHOTS_DIR)) {
    if (!@mkdir(SCREENSHOTS_DIR)) {
        $climate->error("Failed to create directory for screenshots");
        exit(1);
    }
}

try {
    $result = $checker->getBacklinks($url, $pattern, true, false, true);
} catch (RuntimeException $e) {
    $climate->error($e->getMessage());
    exit(1);
}
$response = $result->getResponse();
$screenshot = $response->getScreenshot();
if ($screenshot && strlen($screenshot) > 0) {
    $file_name = preg_replace('/[^a-z0-9]+/', '-', strtolower(html_entity_decode($url)));
    file_put_contents(SCREENSHOTS_DIR . "/" . $file_name . ".jpg", $screenshot);
}
if (!$response->getSuccess()) {
    $climate->error("Error code: " . $response->getStatusCode());
    exit(1);
}

$links = $result->getBacklinks();
$climate->cyan("Found " . sizeof($links) . " backlinks");

foreach ($links as $key => $link) {
    $climate->out("Found <" . $link->getTag() . "> src=" . $link->getBacklink() . " anchor=" . $link->getAnchor());
}
$climate->cyan("All operations complete");