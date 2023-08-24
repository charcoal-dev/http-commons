<?php
/*
 * This file is a part of "charcoal-dev/http-commons" package.
 * https://github.com/charcoal-dev/http-commons
 *
 * Copyright (c) Furqan A. Siddiqui <hello@furqansiddiqui.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit following link:
 * https://github.com/charcoal-dev/http-commons/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Charcoal\HTTP\Commons;

use Charcoal\Charsets\ASCII;

/**
 * Class ReadOnlyPayload
 * @package Charcoal\HTTP\Commons
 */
class ReadOnlyPayload extends AbstractPayload
{
    /**
     * Return un-sanitized value from payload
     * @param string $key
     * @return string|int|float|bool|array|null
     */
    public function getUnsafe(string $key): string|int|float|bool|array|null
    {
        return $this->getValue($key)?->value;
    }

    /**
     * This method first strips any characters having ord value height than 127 in ASCII table, while other
     * non-printable 7-bit characters may be optionally kept (ASCII range < 32, such as line breaks or NULL bytes)
     * if provided in "$allowedLowChars", also any printable 7-bit char (ASCII range 32-127) may be stripped
     * if provided in "$stripChars"
     * @param string $prop
     * @param string|null $allowedLowChars
     * @param string|null $stripChars
     * @param bool $trim
     * @return string
     */
    public function getASCII(string $prop, ?string $allowedLowChars = null, ?string $stripChars = null, bool $trim = true): string
    {
        $value = $this->getUnsafe($prop);
        if (!is_string($value)) {
            return "";
        }

        $value = ASCII::Filter($value, $allowedLowChars, $stripChars);
        if ($trim) {
            $value = trim($value);
        }

        return $value;
    }

    /**
     * @param string $prop
     * @param string|null $allowedLowChars
     * @param string|null $stripChars
     * @return string|int|float|array|bool|null
     */
    public function getSanitized(string $prop, ?string $allowedLowChars = null, ?string $stripChars = null): string|int|float|array|bool|null
    {
        return $this->sanitizeValue($this->getUnsafe($prop), $allowedLowChars, $stripChars);
    }

    /**
     * Gets a value as integer
     * @param string $prop
     * @param bool $unSigned
     * @return int|null
     */
    public function getInt(string $prop, bool $unSigned = false): ?int
    {
        $value = $this->getUnsafe($prop);
        if (is_string($value) && preg_match('/^(0|-?[1-9][0-9]*)$/', $value)) {
            $value = intval($value);
        }

        if (is_int($value)) {
            if ($unSigned) {
                return $value >= 0 ? $value : null;
            }

            return $value;
        }

        return null;
    }

    /**
     * @param mixed $in
     * @param string|null $allowedLowChars
     * @param string|null $stripChars
     * @return string|int|float|bool|array|null
     */
    private function sanitizeValue(
        mixed   $in,
        ?string $allowedLowChars = null,
        ?string $stripChars = null
    ): string|int|float|bool|null|array
    {
        if (is_string($in)) {
            return $this->getASCII($in);
        }

        if (is_scalar($in) || is_null($in)) {
            return $in;
        }

        if (is_array($in)) {
            $sanitized = [];
            foreach ($in as $key => $value) {
                if (!is_string($key) && !is_int($key)) {
                    continue;
                }

                if (is_string($key) && $this->validateKeys && !preg_match($this->validateKeyRegExp, $key)) {
                    continue;
                }

                $sanitized[$key] = $this->sanitizeValue($value, $allowedLowChars, $stripChars);
            }

            return $sanitized;
        }

        return null;
    }
}

