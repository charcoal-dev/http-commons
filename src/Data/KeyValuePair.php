<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Data;

/**
 * Class KeyValuePair
 * @package Charcoal\Http\Commons
 */
readonly class KeyValuePair
{
    public function __construct(
        public string                           $key,
        public string|int|float|bool|null|array $value
    )
    {
    }
}