<?php

namespace Valitov\BacklinkCheckerDemo;

class Base
{
    public const SCREENSHOTS_DIR = __DIR__ . "/screenshots";

    /**
     * End the program with an error message
     * @param string $message - the error message to display
     * @param class-string $e - the exception to throw if the app is running under PHPUnit
     * @param int $exitCode - the exit code to return if the app is not running under PHPUnit
     * @return void
     */
    public static function endProgram(string $message, string $e, int $exitCode = 1): void
    {
        // Check if we run under PHPUnit
        if (defined('PHPUNIT_COMPOSER_INSTALL')) {
            throw new $e($message);
        } else {
            if ($message !== "") {
                echo "$message" . PHP_EOL;
            }
            exit($exitCode);
        }
    }

    /**
     * End the program because of an exception
     * @param \Exception $e - the exception that caused the program to end
     * @param int $exitCode - the exit code to return if the app is not running under PHPUnit
     * @return void
     */
    public static function endProgramException(\Exception $e, int $exitCode = 1): void
    {
        // Check if we run under PHPUnit
        if (defined('PHPUNIT_COMPOSER_INSTALL')) {
            throw $e;
        } else {
            echo $e->getMessage() . PHP_EOL;
            exit($exitCode);
        }
    }
}
