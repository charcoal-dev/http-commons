<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Enums;

/**
 * This enumeration defines the modes of parameter key validation.
 */
enum ParamKeyValidation
{
    case STRICT;
    case REGULAR;
    case UNSANITIZED;

    /**
     * Validates the given key against the specified validation rule.
     */
    public function isValidKey(string $key): bool
    {
        return match ($this) {
            ParamKeyValidation::REGULAR => preg_match('/^[A-Za-z0-9._-]+$/', $key) === 1,
            ParamKeyValidation::STRICT => preg_match('/^[A-Za-z0-9_-]+(?:\.[A-Za-z0-9_-]+)*$/', $key) === 1,
            ParamKeyValidation::UNSANITIZED => true
        };
    }
}