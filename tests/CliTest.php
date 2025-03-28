<?php

//phpcs:ignore
declare(strict_types=1);

namespace Valitov\BacklinkCheckerDemo;

use PHPUnit\Framework\TestCase;

/**
 * Class CliTest
 * Tests direct execution of the CLI script
 */
final class CliTest extends TestCase //phpcs:ignore
{
    public const SCRIPT_FILENAME = __DIR__ . "/../src/cli_test.php";
    public const HOST = "http://127.0.0.1:3000/";
    /**
     * @var string PHP executable with a proper version and script filename
     */
    public string $phpExecutable;

    protected function setUp(): void
    {
        $this->phpExecutable = PHP_BINARY . " " . self::SCRIPT_FILENAME;
    }

    /**
     * Tests for links
     * @return void
     */
    public function testLinks(): void
    {
        $url = self::HOST . "links.html";
        $engines = [
            "simple",
            "javascript",
        ];
        foreach ($engines as $engine) {
            exec("$this->phpExecutable -u \"$url\" -p \"@.*@\" -m $engine", $output, $exitCode);
            $this->assertIsArray($output, "Failed to get the output from the script");
            // Merge the output array into a single string
            $output = implode("\n", $output);
            $this->assertEquals(0, $exitCode, "Exit code is not 0" . PHP_EOL . $output);
            $this->assertStringContainsString("Using mode: $engine", $output, "Invalid mode");
            $this->assertStringContainsString("Found 2 backlinks", $output, "Invalid number of backlinks");
        }
    }
}
