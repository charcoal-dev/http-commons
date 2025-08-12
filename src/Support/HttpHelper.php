<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Support;

use Charcoal\Base\Enums\Charset;
use Charcoal\Http\Commons\Enums\HttpHeaderKeyPolicy;

/**
 * Class HttpHelper
 * @package Charcoal\Http\Commons\Support
 */
class HttpHelper
{
    /**
     * @param string $name
     * @param HttpHeaderKeyPolicy $policy
     * @return bool
     */
    public static function isValidHeaderName(string $name, HttpHeaderKeyPolicy $policy): bool
    {
        return match ($policy) {
            HttpHeaderKeyPolicy::RFC7230 => (bool)preg_match('/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/', $name),
            HttpHeaderKeyPolicy::STRICT => (bool)preg_match('/^[\w\-.]+$/', $name),
            HttpHeaderKeyPolicy::UNSANITIZED => true
        };
    }

    /**
     * @param string $value
     * @param Charset $charset
     * @return bool
     */
    public static function isValidHeaderValue(string $value, Charset $charset = Charset::ASCII): bool
    {
        return match ($charset) {
            Charset::ASCII => preg_match('/\A[\x20-\x7E]*\z/', $value) === 1,
            Charset::UTF8 => preg_match('/\A[^\x00-\x1F\x7F\x{0080}-\x{009F}]*\z/u', $value) === 1,
        };
    }
}