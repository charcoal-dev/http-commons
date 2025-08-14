<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Enums;

/**
 * Class Http
 * @package Charcoal\Http\Commons\Enums
 */
enum Http: int
{
    case Version1 = 100;
    case Version1_1 = 110;
    case Version2 = 200;
    case Version3 = 300;
}