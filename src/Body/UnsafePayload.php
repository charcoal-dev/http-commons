<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Body;

use Charcoal\Charsets\Support\AsciiHelper;
use Charcoal\Charsets\Support\Utf8Helper;
use Charcoal\Contracts\Charsets\Charset;
use Charcoal\Http\Commons\Enums\ParamKeyValidation;

/**
 * Class UnsafePayload
 * @package Charcoal\Http\Commons\Body
 */
class UnsafePayload extends Payload
{
    /**
     * @param string $param
     * @return int|string|float|bool|array|null
     */
    public function getUnsafe(string $param): int|string|float|bool|null|array
    {
        return $this->getEntry($param)?->value ?? null;
    }

    /**
     * @param string $param
     * @param bool $tabChar
     * @param bool $lineBreaks
     * @param bool $nullByte
     * @param bool $trim
     * @return string
     */
    public function getAscii(
        string $param,
        bool   $tabChar = false,
        bool   $lineBreaks = false,
        bool   $nullByte = false,
        bool   $trim = true
    ): string
    {
        $value = $this->getUnsafe($param);
        if (!is_string($value)) {
            return "";
        }

        $value = AsciiHelper::sanitizeUseRegEx($param, $tabChar, $lineBreaks, $nullByte);
        if ($trim) {
            $value = trim($value);
        }

        return $value;
    }

    /**
     * @param string $prop
     * @param bool $strict
     * @return int|null
     */
    public function getInt(string $prop, bool $strict = false): ?int
    {
        $value = $this->getUnsafe($prop);
        if (is_int($value)) {
            return $value;
        }

        if (!$strict && (is_string($value) && preg_match('/^(0|-?[1-9][0-9]*)$/', $value))) {
            return intval($value);
        }

        return null;
    }

    /**
     * @param string ...$keys
     * @return array
     */
    public function getUnrecognizedKeys(string ...$keys): array
    {
        return array_values(array_diff(array_keys($this->dataset), $keys));
    }

    /**
     * @param string ...$keys
     * @return bool
     */
    public function isRestrictedToKeys(string ...$keys): bool
    {
        return empty($this->getUnrecognizedKeys(...$keys));
    }

    /**
     * @param string $param
     * @param Charset $charset
     * @return string|int|float|bool|array|null
     */
    public function getSanitized(string $param, Charset $charset): string|int|float|bool|null|array
    {
        return $this->sanitizeValue($this->getUnsafe($param), $charset);
    }

    /**
     * @param mixed $value
     * @param Charset $charset
     * @return string|int|float|bool|array|null
     */
    private function sanitizeValue(mixed $value, Charset $charset): string|int|float|bool|null|array
    {
        if (is_string($value)) {
            $value = match ($charset) {
                Charset::ASCII => AsciiHelper::sanitizeUseRegEx($value),
                Charset::UTF8 => Utf8Helper::filterOutExtras($value, true, true, ...$this->unicodeRanges),
            };

            return trim($value);
        }

        if (is_scalar($value) || is_null($value)) {
            return $value;
        }

        if (is_array($value)) {
            $sanitized = [];
            foreach ($value as $key => $item) {
                if (!is_string($key) && !is_int($key)) {
                    continue;
                }

                if (!ParamKeyValidation::REGULAR->isValidKey($key)) {
                    continue;
                }

                $sanitized[$key] = $this->sanitizeValue($value, $charset);
            }

            return $sanitized;
        }

        return null;
    }
}