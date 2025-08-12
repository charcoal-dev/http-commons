<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Enums;

/**
 * Class HttpHeaderKeySpec
 * @package Charcoal\Http\Commons\Enums
 */
enum HttpHeaderKeyPolicy
{
    case RFC7230;
    case STRICT;
}