<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Enums;

/**
 * Enum representing HTTP methods as string values.
 * Includes common methods such as GET, POST, PUT, DELETE, and OPTIONS.
 */
enum HttpMethod: string
{
    case GET = "GET";
    case POST = "POST";
    case PUT = "PUT";
    case DELETE = "DELETE";
    case HEAD = "HEAD";
    case PATCH = "PATCH";
    case OPTIONS = "OPTIONS";

    /**
     * Case-insensitive method lookup.
     */
    public static function find(string $method): ?self
    {
        return self::tryFrom(strtoupper(trim($method)));
    }
}
