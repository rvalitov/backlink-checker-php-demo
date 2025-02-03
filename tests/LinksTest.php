<?php

//phpcs:ignore
declare(strict_types=1);

namespace Valitov\BacklinkCheckerDemo;

require_once __DIR__ . "/../src/Base.php";

use GuzzleHttp\Exception\ConnectException;
use phpmock\Mock;
use PHPUnit\Framework\TestCase;

/**
 * Class LinksTest
 * Tests the code base for detecting links
 */
final class LinksTest extends TestCase //phpcs:ignore
{
    use \phpmock\phpunit\PHPMock;

    public const SCRIPT_FILENAME = __DIR__ . "/../src/cli_test.php";
    public const HOST = "http://127.0.0.1:3000/";

    public function setUp(): void
    {
        ob_start();
    }

    public function tearDown(): void
    {
        ob_end_clean();
    }

    /**
     * Tests for one link
     * @return void
     */
    public function testLink(): void
    {
        $engines = [
            "simple",
            "javascript",
        ];

        foreach ($engines as $engine) {
            $getOpt = $this->getFunctionMock("Valitov\BacklinkCheckerDemo", "getopt");
            $getOpt->expects($this->once())->willReturn([
                "u" => self::HOST . "link.html",
                "p" => "@.*@",
                "m" => $engine,
            ]);

            ob_clean();
            require self::SCRIPT_FILENAME;
            $output = ob_get_contents();
            $this->assertStringContainsString("Found 1 backlinks", $output);
            $this->assertStringContainsString("Found <a> src=https://example.com anchor=Click here", $output);
            $this->assertStringContainsString("All operations complete", $output);
            Mock::disableAll();
            unset($getOpt);
        }
    }

    /**
     * Tests for multiple links
     * @return void
     */
    public function testLinks(): void
    {
        $engines = [
            "simple",
            "javascript",
        ];

        foreach ($engines as $engine) {
            $getOpt = $this->getFunctionMock("Valitov\BacklinkCheckerDemo", "getopt");
            $getOpt->expects($this->once())->willReturn([
                "u" => self::HOST . "links.html",
                "p" => "@.*@",
                "m" => $engine,
            ]);

            ob_clean();
            require self::SCRIPT_FILENAME;
            $output = ob_get_contents();
            $this->assertStringContainsString("Found 2 backlinks", $output);
            $this->assertStringContainsString("Found <a> src=https://example.com anchor=First", $output);
            $this->assertStringContainsString("Found <a> src=https://example2.com anchor=Second", $output);
            $this->assertStringContainsString("All operations complete", $output);
            Mock::disableAll();
            unset($getOpt);

            $getOpt = $this->getFunctionMock("Valitov\BacklinkCheckerDemo", "getopt");
            $getOpt->expects($this->once())->willReturn([
                "u" => self::HOST . "links.html",
                "p" => "@https:\/\/example2\.com@",
                "m" => $engine,
            ]);

            ob_clean();
            require self::SCRIPT_FILENAME;
            $output = ob_get_contents();
            $this->assertStringContainsString("Found 1 backlinks", $output);
            $this->assertStringContainsString("Found <a> src=https://example2.com anchor=Second", $output);
            $this->assertStringContainsString("All operations complete", $output);
            Mock::disableAll();
            unset($getOpt);
        }
    }

    /**
     * Tests for no links
     * @return void
     */
    public function testNoLinks(): void
    {
        $engines = [
            "simple",
            "javascript",
        ];
        foreach ($engines as $engine) {
            $getOpt = $this->getFunctionMock("Valitov\BacklinkCheckerDemo", "getopt");
            $getOpt->expects($this->once())->willReturn([
                "u" => self::HOST . "nolink.html",
                "p" => "@.*@",
                "m" => $engine,
            ]);

            ob_clean();
            require self::SCRIPT_FILENAME;
            $output = ob_get_contents();
            $this->assertStringContainsString("Found 0 backlinks", $output);
            $this->assertStringContainsString("All operations complete", $output);
            Mock::disableAll();
            unset($getOpt);

            $getOpt = $this->getFunctionMock("Valitov\BacklinkCheckerDemo", "getopt");
            $getOpt->expects($this->once())->willReturn([
                "u" => self::HOST . "links.html",
                "p" => "@https:\/\/example10\.com@",
                "m" => $engine,
            ]);

            ob_clean();
            require self::SCRIPT_FILENAME;
            $output = ob_get_contents();
            $this->assertStringContainsString("Found 0 backlinks", $output);
            $this->assertStringContainsString("All operations complete", $output);
            Mock::disableAll();
            unset($getOpt);
        }
    }

    /**
     * Tests for screenshots
     * @return void
     */
    public function testScreenshot(): void
    {
        // Delete all files in the screenshots directory
        $files = glob(Base::SCREENSHOTS_DIR . "/*");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $getOpt = $this->getFunctionMock("Valitov\BacklinkCheckerDemo", "getopt");
        $getOpt->expects($this->once())->willReturn([
            "u" => self::HOST . "link.html",
            "p" => "@.*@",
            "m" => "javascript",
        ]);

        ob_clean();
        require self::SCRIPT_FILENAME;
        Mock::disableAll();
        unset($getOpt);
        $output = ob_get_contents();
        $this->assertStringContainsString("Found <", $output);
        $this->assertStringContainsString("All operations complete", $output);

        // Check if the screenshot was saved
        $files = glob(Base::SCREENSHOTS_DIR . "/*");
        $this->assertNotEmpty($files, "Screenshot was not saved");
        // There should be only one file
        $this->assertCount(1, $files, "More than one screenshot was saved");
        // Check if the file is a JPEG image
        $this->assertStringEndsWith(".jpg", $files[0], "Screenshot does not have a .jpg extension");
        // Check if the file is a valid image
        $this->assertNotFalse(imagecreatefromjpeg($files[0]), "Screenshot is not a valid JPEG image");
    }

    /**
     * Tests for JS generated link
     * @return void
     */
    public function testJsLink(): void
    {
        // In simple mode, we can detect only one link
        $getOpt = $this->getFunctionMock("Valitov\BacklinkCheckerDemo", "getopt");
        $getOpt->expects($this->once())->willReturn([
            "u" => self::HOST . "js.html",
            "p" => "@.*@",
            "m" => "simple",
        ]);

        ob_clean();
        require self::SCRIPT_FILENAME;
        Mock::disableAll();
        unset($getOpt);
        $output = ob_get_contents();
        $this->assertStringContainsString("Found 1 backlinks", $output);
        $this->assertStringContainsString("Found <a> src=https://example.com anchor=Static", $output);
        $this->assertStringContainsString("All operations complete", $output);

        // In JS mode, we can detect both links
        $getOpt = $this->getFunctionMock("Valitov\BacklinkCheckerDemo", "getopt");
        $getOpt->expects($this->once())->willReturn([
            "u" => self::HOST . "js.html",
            "p" => "@.*@",
            "m" => "javascript",
        ]);

        ob_clean();
        require self::SCRIPT_FILENAME;
        Mock::disableAll();
        unset($getOpt);
        $output = ob_get_contents();
        $this->assertStringContainsString("Found 2 backlinks", $output);
        $this->assertStringContainsString("Found <a> src=https://example.com anchor=Static", $output);
        $this->assertStringContainsString("Found <a> src=https://example.com anchor=Dynamic", $output);
        $this->assertStringContainsString("All operations complete", $output);
    }
}
