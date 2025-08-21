<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Enums;

use Charcoal\Http\Commons\Contracts\AuthSchemeEnumInterface;

/**
 * Class Authentication
 * @package Charcoal\Http\Commons\Enums
 */
enum Authentication: string implements AuthSchemeEnumInterface
{
    case Basic = "Basic";
    case Digest = "Digest";
    case Custom = "Custom";
}