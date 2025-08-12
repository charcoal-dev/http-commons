<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Enums;

/**
 * Class HttpMethod
 * @package Charcoal\Http\Commons\Enums
 */
enum HttpMethod: string
{
    case GET = "GET";
    case POST = "POST";
    case PUT = "PUT";
    case DELETE = "DELETE";
    case OPTIONS = "OPTIONS";
}
