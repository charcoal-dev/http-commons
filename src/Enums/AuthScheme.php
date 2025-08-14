<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Enums;

use Charcoal\Http\Commons\Contracts\AuthSchemeEnumInterface;

/**
 * Class AuthScheme
 * @package Charcoal\Http\Router\Enums
 */
enum AuthScheme implements AuthSchemeEnumInterface
{
    case Basic;
    case Digest;
    case Custom;

    /**
     * @return string
     */
    public function scheme(): string
    {
        return strtolower($this->name);
    }
}