<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Enums;

/**
 * Class HttpHeaderKeyPolicy
 * @package Charcoal\Http\Commons\Enums
 */
enum HeaderKeyPolicy
{
    case STRICT;
    case RFC7230;
    case UNSANITIZED;
}