<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Enums;

/**
 * Represents the HTTP protocol versions as an enumeration.
 */
enum HttpProtocol: string
{
    case Version1 = "HTTP/1.0";
    case Version1_1 = "HTTP/1.1";
    case Version2 = "HTTP/2";
    case Version3 = "HTTP/3";

    /**
     * Case-insensitive protocol lookup.
     */
    public static function find(string $protocol): ?self
    {
        $protocol = strtoupper(trim($protocol));
        return match ($protocol) {
            "HTTP/2.0", "HTTP2" => self::Version2,
            "HTTP/3.0", "HTTP3" => self::Version3,
            default => self::tryFrom($protocol),
        };
    }
}