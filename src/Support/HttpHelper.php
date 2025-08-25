<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Support;

use Charcoal\Base\Support\Helpers\NetworkHelper;

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
     * Normalizes a hostname by removing any trailing slashes and converting it to lowercase.
     * Returns validated hostname at index 0, and port if found at index 1, otherwise returns false.
     * If the hostname is a bracketed IPv6 address, expect boolean true at index 2.
     * @param string $hostname
     * @return array<string,int|null>|false
     */
    public static function normalizeHostnamePort(string $hostname): array|false
    {
        $hostname = trim($hostname);
        if ($hostname === "") {
            return false;
        }

        // Bracketed IPv6 addresses
        if (str_starts_with($hostname, "[")) {
            $brackets = strpos($hostname, "]");
            if (!$brackets) {
                return false;
            }

            $baggage = substr($hostname, $brackets + 1);
            $hostname = substr($hostname, 1, $brackets - 1);
            if ($baggage) {
                if ($baggage[0] !== ":") {
                    return false;
                }

                $port = substr($baggage, 1);
                if (ctype_digit($port)) {
                    $port = (int)$port;
                }
            }
        }

        // Suffixed Port
        if (str_contains($hostname, ":") && !isset($brackets)) {
            $parts = explode(":", $hostname);
            if (count($parts) !== 2 || !ctype_digit($parts[1])) {
                return false;
            }

            $hostname = $parts[0];
            $port = (int)$parts[1];
        }

        // Validate Hostname
        $hostname = strtolower(trim($hostname, "."));
        if (!NetworkHelper::isValidHostname($hostname, allowIpAddr: true, allowNonTld: true)) {
            return false;
        }

        return [$hostname, match (true) {
            isset($port) && $port >= 1 && $port <= 65535 => $port,
            default => null,
        }, isset($brackets)];
    }

    /**
     * Reconstructs a hostname and optional port from the provided parts.
     * Returns the reassembled string or false on failure.
     */
    public static function rejoinValidatedParts(null|false|array $parts): string|false
    {
        if (!is_array($parts) || !$parts) {
            return false;
        }

        return match (true) {
            count($parts) === 1 => $parts[0],
            count($parts) === 3 && $parts[1] && !$parts[2] => $parts[0] . ":" . $parts[1],
            $parts[2] === true => "[" . $parts[0] . "]:" . $parts[1],
            default => false,
        };
    }
}