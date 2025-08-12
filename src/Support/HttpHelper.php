<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Support;

use Charcoal\Http\Commons\Enums\HttpHeaderKeyPolicy;

/**
 * Class HttpHelper
 * @package Charcoal\Http\Commons\Support
 */
class HttpHelper
{
    /**
     * @param string $name
     * @param HttpHeaderKeyPolicy $spec
     * @return bool
     */
    public static function isValidHeaderName(string $name, HttpHeaderKeyPolicy $spec): bool
    {
        return match ($spec) {
            HttpHeaderKeyPolicy::RFC7230 => (bool)preg_match('/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/', $name),
            default => (bool)preg_match('/^[\w\-.]+$/', $name)
        };
    }
}