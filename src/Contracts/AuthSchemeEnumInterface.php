<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Contracts;

/**
 * Interface AuthSchemeEnumInterface
 * @package Charcoal\Http\Commons\Contracts
 */
interface AuthSchemeEnumInterface extends \UnitEnum
{
    public function scheme(): string;
}