<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Data;

use Charcoal\Base\Enums\ValidationState;

/**
 * Class HttpDataPolicy
 * @package Charcoal\Http\Commons\Data
 */
readonly class HttpDataPolicy
{
    public function __construct(
        public int             $keyMaxLength = 64,
        public bool            $keyOverflowTrim = false,
        public int             $valueMaxLength = 2048,
        public bool            $valueOverflowTrim = false,
        public ValidationState $trust = ValidationState::RAW,
        public bool            $countLengthUtf8 = false,
    )
    {
    }
}