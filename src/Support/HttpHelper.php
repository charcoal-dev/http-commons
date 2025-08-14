<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Support;

use Charcoal\Base\Enums\Charset;
use Charcoal\Http\Commons\Enums\HeaderKeyPolicy;
use Charcoal\Http\Commons\Enums\ParamKeyPolicy;

/**
 * Class HttpHelper
 * @package Charcoal\Http\Commons\Support
 */
class HttpHelper
{
    /**
     * @param string $name
     * @param HeaderKeyPolicy $policy
     * @return bool
     */
    public static function isValidHeaderName(string $name, HeaderKeyPolicy $policy): bool
    {
        return match ($policy) {
            HeaderKeyPolicy::RFC7230 => (bool)preg_match('/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/', $name),
            HeaderKeyPolicy::STRICT => (bool)preg_match('/^[\w\-.]+$/', $name),
            HeaderKeyPolicy::UNSANITIZED => true
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

    /**
     * @param string $key
     * @param ParamKeyPolicy $policy
     * @return bool
     */
    public static function isValidParamKey(string $key, ParamKeyPolicy $policy): bool
    {
        return match ($policy) {
            ParamKeyPolicy::REGULAR => preg_match('/\A[A-Za-z0-9._-]+\z/', $key) === 1,
            ParamKeyPolicy::STRICT => preg_match('/\A[A-Za-z0-9_-]+(?:\.[A-Za-z0-9_-]+)*\z/', $key) === 1,
            ParamKeyPolicy::UNSANITIZED => true
        };
    }
}