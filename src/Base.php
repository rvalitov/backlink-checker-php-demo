<?php

namespace Valitov\BacklinkCheckerDemo;

class Base
{
    public const SCREENSHOTS_DIR = __DIR__ . "/screenshots";

    /**
     * End the program with an error message
     * @param string $message - the error message to display
     * @param class-string $exceptionClass - the exception to throw if the app is running under PHPUnit
     * @param int $exitCode - the exit code to return if the app is not running under PHPUnit
     * @return void
     * @SuppressWarnings(PHPMD.ExitExpression)
     * @psalm-suppress InvalidThrow
     */
    public static function endProgram(string $message, string $exceptionClass, int $exitCode = 1): void
    {
        // Check if we run under PHPUnit
        if (defined('PHPUNIT_COMPOSER_INSTALL')) {
            throw new $exceptionClass($message);
        } else {
            if ($message !== "") {
                echo "$message" . PHP_EOL;
            }
            exit($exitCode);
        }
    }

    /**
     * End the program because of an exception
     * @param \Exception $exception - the exception that caused the program to end
     * @param int $exitCode - the exit code to return if the app is not running under PHPUnit
     * @return void
     * @SuppressWarnings(PHPMD.ExitExpression)
     * @psalm-suppress InvalidThrow
     */
    public static function endProgramException(\Exception $exception, int $exitCode = 1): void
    {
        // Check if we run under PHPUnit
        if (defined('PHPUNIT_COMPOSER_INSTALL')) {
            throw $exception;
        } else {
            echo $exception->getMessage() . PHP_EOL;
            exit($exitCode);
        }
    }
}
