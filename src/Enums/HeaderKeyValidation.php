<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Enums;

use Charcoal\Base\Enums\Charset;

/**
 * - STRICT: Enforces strict compliance with header key naming rules.
 * - RFC7230: Adheres to the conventions outlined in the RFC 7230 specification.
 * - UNSANITIZED: Allows header keys to remain unvalidated or unmodified.
 */
enum HeaderKeyValidation: string
{
    case STRICT = "strict";
    case RFC7230 = "rfc7230";
    case UNSANITIZED = "unsanitized";

    /**
     * Validates the given name against the specified validation rule.
     */
    public function isValidName(string $name): bool
    {
        return match ($this) {
            HeaderKeyValidation::RFC7230 => (bool)preg_match('/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/', $name),
            HeaderKeyValidation::STRICT => (bool)preg_match('/^[0-9A-Za-z_.-]+$/', $name),
            HeaderKeyValidation::UNSANITIZED => true
        };
    }

    /**
     * Validates the given string value based on the specified character set.
     */
    public function isValidValue(string $value, Charset $charset = Charset::ASCII): bool
    {
        return match ($charset) {
            Charset::ASCII => preg_match('/^[\x20-\x7E]*$/', $value) === 1,
            Charset::UTF8 => preg_match('/^[^\x00-\x1F\x7F\x{0080}-\x{009F}]*$/u', $value) === 1,
        };
    }
}