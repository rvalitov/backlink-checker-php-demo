<?php

//phpcs:ignore
declare(strict_types=1);

namespace Valitov\BacklinkCheckerDemo;

require_once __DIR__ . "/../src/Base.php";

use GuzzleHttp\Exception\ConnectException;
use phpmock\Mock;
use PHPUnit\Framework\TestCase;

define("USING_MODE_SIMPLE", "Using mode: simple");
define("USING_MODE_JAVASCRIPT", "Using mode: javascript");

/**
 * Class BasicTest
 * Tests the code base with the basic functionality
 */
final class BasicTest extends TestCase //phpcs:ignore
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
     * Tests when required options are missing
     * @return void
     */
    public function testEmpty(): void
    {
        $optionsList = [
            false,
            [],
            ["u" => "https://example.com"],
            ["p" => "test"],
            ["u" => ""],
            ["u" => false],
            ["p" => ""],
            ["p" => false],
            ["u" => "", "p" => ""],
        ];

        foreach ($optionsList as $options) {
            $getOpt = $this->getFunctionMock(__NAMESPACE__, "getopt");
            $getOpt->expects($this->once())->willReturn($options);

            $this->expectException(\InvalidArgumentException::class);
            require self::SCRIPT_FILENAME;
            Mock::disableAll();
            unset($getOpt);
        }
    }

    /**
     * Tests for an invalid regex pattern
     * @return void
     */
    public function testInvalidPattern()
    {
        $invalidPatterns = [
            false,
            '/unclosed[/',             // Unclosed character class
            '/(unclosed group/',       // Unclosed parenthesis
            '/unbalanced){2}/',        // Unmatched closing parenthesis
            '/[a-z/',                  // Unterminated character class
            '/(foo(bar)/',             // Missing closing parenthesis
            '/foo(bar))/',             // Extra closing parenthesis
            '/{2,1}/',                 // Invalid quantifier (min > max)
            '/foo{1,}/+',              // Invalid double quantifiers
            '/(?P<name>foo)(?P<name>bar)/',  // Duplicate named capture group
            '/(?R/',                   // Unclosed recursive pattern
            '/\p{InvalidCategory}/u',  // Invalid Unicode property
            '/\c/',                    // Invalid control character
            '/(?z)/',                  // Unknown or unsupported modifier
            '/foo(?=bar/',             // Unclosed lookahead assertion
            '/(?<=foo/',               // Unclosed lookbehind assertion
            '/foo)(bar/',              // Unbalanced parentheses
            '/(?-i/foo)/',             // Invalid use of mode modifier
            '/[\w-\d]/',               // Contradictory character class
        ];

        foreach ($invalidPatterns as $pattern) {
            $getOpt = $this->getFunctionMock(__NAMESPACE__, "getopt");
            $getOpt->expects($this->once())->willReturn([
                "u" => "https://example.com",
                "p" => $pattern,
            ]);

            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessageMatches("/.*RegExp pattern.*/");
            require self::SCRIPT_FILENAME;
            Mock::disableAll();
            unset($getOpt);
        }
    }

    /**
     * Tests for invalid URLs
     * @return void
     */
    public function testInvalidUrl(): void
    {
        $urls = [
            'https://',                      // No domain
            'https:// example.com',          // Space before domain
            'https://example..com',          // Double dot in domain
            'https://.example.com',          // Leading dot in domain
            'https://example.com/',          // Valid, but let's test with an extra space
            'https://example.com/ page',     // Space in path
            'https://example.com/<>',        // Invalid characters in path
            'https://exam_ple.com',          // Underscore in domain (not allowed)
            'https://example,com',           // Comma instead of dot
            'https://256.256.256.256',       // Invalid IP address
            'https://localhost:99999',       // Invalid port (out of range)
            'https://user:pass@example.com:abc', // Non-numeric port
            'https://www.exa mple.com',      // Space inside domain
            'https://example.com#fragment',  // Fragment in URL (depends on validation rules)
            'https://-example.com',         // Leading hyphen in domain
            'https://example-.com',          // Trailing hyphen in domain
            'https://example..com',          // Consecutive dots
            'https://.com',                  // Missing domain name
            'https://?query=string',         // Missing domain before query string
            'https://example.com:abcd',      // Non-numeric port
            'https:///example.com',          // Extra slash in scheme
        ];

        foreach ($urls as $url) {
            $getOpt = $this->getFunctionMock(__NAMESPACE__, "getopt");
            $getOpt->expects($this->once())->willReturn([
                "u" => $url,
                "p" => "/.*/",
            ]);

            $this->expectExceptionMessage("Invalid URL");
            require self::SCRIPT_FILENAME;
            Mock::disableAll();
            unset($getOpt);
        }
    }

    /**
     * Tests for invalid SAPI.
     * The app supports CLI mode only.
     * @return void
     */
    public function testInvalidSapi(): void
    {
        $phpSapiName = $this->getFunctionMock(__NAMESPACE__, "php_sapi_name");
        $phpSapiName->expects($this->once())->willReturn("php-fpm");

        $this->expectExceptionMessage("This script can run in CLI mode only");
        require self::SCRIPT_FILENAME;
    }

    public function testProtocols(): void
    {
        $urls = [
            "example.com",
            'https:/example.com',   // Missing slash in scheme
            '://example.com',       // Missing scheme
            'https//example.com',   // Missing colon after "https"
            'ftp://example.com',    // Non-HTTP(S) scheme (if restricting to HTTP/HTTPS)
            'mailto://example.com', // Non-HTTP(S) scheme (if restricting to HTTP/HTTPS)
        ];

        foreach ($urls as $url) {
            $getOpt = $this->getFunctionMock(__NAMESPACE__, "getopt");
            $getOpt->expects($this->once())->willReturn([
                "u" => $url,
                "p" => "/.*/",
            ]);

            $this->expectExceptionMessage("HTTP or HTTPS protocol is required to be specified");
            require self::SCRIPT_FILENAME;
            Mock::disableAll();
            unset($getOpt);
        }
    }

    /**
     * Tests for invalid modes
     * @return void
     */
    public function testInvalidModes(): void
    {
        $getOpt = $this->getFunctionMock(__NAMESPACE__, "getopt");
        $this->expectExceptionMessageMatches("/^Invalid value for parameter mode/");
        $getOpt->expects($this->once())->willReturn([
            "u" => self::HOST,
            "p" => "@.*@",
            "m" => "invalid",
        ]);
        require self::SCRIPT_FILENAME;
        Mock::disableAll();
        unset($getOpt);
    }

    /**
     * Tests for different modes
     * @return void
     */
    public function testValidModes(): void
    {
        $modes = [
            [
                "simple",
                USING_MODE_SIMPLE,
            ],
            [
                "Simple",
                USING_MODE_SIMPLE,
            ],
            [
                " Simple  ",
                USING_MODE_SIMPLE,
            ],
            [
                "javascript",
                USING_MODE_JAVASCRIPT,
            ],
            [
                "",
                USING_MODE_JAVASCRIPT,
            ],
            [
                "none",
                USING_MODE_JAVASCRIPT,
            ],
        ];

        foreach ($modes as $mode) {
            $options = [
                "u" => self::HOST,
                "p" => "@.*@",
            ];
            if ($mode[0] !== "none") {
                $options["m"] = $mode[0];
            }
            $getOpt = $this->getFunctionMock(__NAMESPACE__, "getopt");
            $getOpt->expects($this->once())->willReturn($options);

            ob_clean();
            require self::SCRIPT_FILENAME;
            $output = ob_get_contents();
            $this->assertStringContainsString($mode[1], $output, "Failed to test mode \"$mode[0]\"");
            Mock::disableAll();
            unset($getOpt);
        }
    }

    /**
     * Tests for invalid websites
     * @return void
     */
    public function testInvalidWebsite(): void
    {
        $getOpt = $this->getFunctionMock(__NAMESPACE__, "getopt");
        $getOpt->expects($this->once())->willReturn([
            "u" => "https://some-missing-domain-in-internet.com",
            "p" => "/.*/",
            "m" => "simple",
        ]);

        $this->expectException(ConnectException::class);
        require self::SCRIPT_FILENAME;
        Mock::disableAll();
        unset($getOpt);
    }
}
