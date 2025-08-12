<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Enums;

/**
 * Class HttpParamKeyPolicy
 * @package Charcoal\Http\Commons\Enums
 */
enum HttpParamKeyPolicy
{
    case STRICT;
    case REGULAR;
    case UNSANITIZED;
}