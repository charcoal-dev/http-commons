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
}