<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Support;

/**
 * Provides helper methods for HTTP-related operations such as normalizing hostnames.
 */
abstract readonly class HttpHelper
{
    /**
     * @param mixed $origin
     * @return bool
     */
    public static function isValidOrigin(mixed $origin): bool
    {
        if (!is_string($origin) || trim($origin) === "") {
            return false;
        }

        if (preg_match('/\A(https?):\/\/([a-zA-Z0-9.-]+)(:\d+)?\z/', $origin)) {
            return true;
        }

        return false;
    }

    /**
     * Parses a query string into an associative array, decoding keys and values as specified.
     * Safer alternative to PHP parse_str().
     */
    public static function parseQueryString(
        string $queryStr,
        bool   $plusAsSpace = false,
        bool   $utf8Encoding = true,
        int    $maxKeyLength = 64,
        int    $maxValueLength = 256,
        bool   $flatten = true,
    ): array
    {
        if ($queryStr === "") {
            return [];
        }

        if (str_contains($queryStr, ";")) {
            throw new \InvalidArgumentException("Semicolon separator is not allowed");
        }

        if (preg_match("/%(?![0-9A-Fa-f]{2})/", $queryStr) === 1) {
            throw new \InvalidArgumentException("Malformed percent-encoding");
        }

        $parsed = [];
        $index = -1;
        foreach (explode("&", $queryStr) as $pair) {
            $index++;
            if ($pair === "") {
                continue;
            }

            $kRaw = $pair;
            $vRaw = "";
            $eq = strpos($pair, "=");
            if ($eq !== false) {
                $kRaw = substr($pair, 0, $eq);
                $vRaw = substr($pair, $eq + 1);
            }

            if ($plusAsSpace) {
                if (str_contains($kRaw, "+")) $kRaw = str_replace("+", "\x20", $kRaw);
                if (str_contains($vRaw, "+")) $vRaw = str_replace("+", "\x20", $vRaw);
            }

            // RFC 3986 decoding (does not touch "+")
            $key = trim(rawurldecode($kRaw));
            $val = trim(rawurldecode($vRaw));

            // A key cannot be empty (post-decode) and must not contain whitespace
            if (!$key) {
                throw new \InvalidArgumentException("Empty key is not allowed at index " . $index);
            } elseif (preg_match("/\s/u", $key)) {
                throw new \InvalidArgumentException("Key at index " . $index . " contains whitespace");
            }

            // Encoding Checks
            if ($utf8Encoding) {
                if (!mb_check_encoding($key, "UTF-8")) {
                    throw new \InvalidArgumentException(sprintf("Key at index %d is not valid UTF-8", $index));
                }
                if (!mb_check_encoding($val, "UTF-8")) {
                    throw new \InvalidArgumentException(sprintf('Value for "%s" is not valid UTF-8', $key));
                }
            }

            // Key Validations
            if (preg_match("/[\x00-\x1F\x7F]/", $key)) {
                throw new \InvalidArgumentException("Control character in key at index " . $index);
            } elseif (strlen($key) > $maxKeyLength) {
                throw new \InvalidArgumentException("Key at index " . $index . " is too long");
            }

            // Value Validations
            if (preg_match("/[\x00-\x1F\x7F]/", $val)) {
                throw new \InvalidArgumentException("Control character in value for key: " . $key);
            } elseif ($maxValueLength > 0 && strlen($val) > $maxValueLength) {
                throw new \InvalidArgumentException("Value for key: " . $key . " is too long");
            }

            $parsed[$key] ??= [];
            $parsed[$key][] = $val;
        }

        if ($flatten && $parsed) {
            foreach ($parsed as $key => $value) {
                if (count($value) === 1) {
                    $parsed[$key] = $value[0];
                }
            }
        }

        return $parsed;
    }
}