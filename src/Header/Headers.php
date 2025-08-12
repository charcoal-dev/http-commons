<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Header;

use Charcoal\Base\Enums\Charset;
use Charcoal\Base\Enums\ValidationState;
use Charcoal\Http\Commons\Data\AbstractHttpData;
use Charcoal\Http\Commons\Enums\HttpHeaderKeyPolicy;
use Charcoal\Http\Commons\Exception\InvalidHeaderNameException;
use Charcoal\Http\Commons\Exception\InvalidHeaderValueException;
use Charcoal\Http\Commons\Support\HttpHelper;

/**
 * Class Headers
 * @package Charcoal\Http\Commons\Data\Header
 */
class Headers extends AbstractHttpData
{
    public function __construct(
        public readonly HttpHeaderKeyPolicy $keyPolicy = HttpHeaderKeyPolicy::STRICT,
        int                                 $keyMaxLength = 64,
        bool                                $keyOverflowTrim = false,
        Charset                             $valueCharset = Charset::ASCII,
        int                                 $valueMaxLength = 2048,
        bool                                $valueOverflowTrim = true,
        ValidationState                     $writeTrust = ValidationState::RAW,
        ValidationState                     $accessTrust = ValidationState::RAW,
    )
    {
        parent::__construct($keyMaxLength, $keyOverflowTrim, $valueCharset, $valueMaxLength,
            $valueOverflowTrim, $writeTrust, $accessTrust);
    }

    /**
     * @throws InvalidHeaderNameException
     */
    protected function validateEntityKeyFn(string $name): string
    {
        if (!HttpHelper::isValidHeaderName($name, $this->keyPolicy)) {
            throw new InvalidHeaderNameException("Encountered invalid header name", $name);
        }

        if (strlen($name) > $this->keyMaxLength) {
            if (!$this->keyOverflowTrim) {
                throw new InvalidHeaderNameException("Header name exceeds maximum length", $name);
            }

            return substr($name, 0, $this->keyMaxLength);
        }

        return $name;
    }

    /**
     * @throws InvalidHeaderValueException
     */
    protected function validateEntityValueFn(mixed $value, string $name): string
    {
        if (!is_string($value)) {
            throw new InvalidHeaderValueException("Header value must be a string", $name);
        }

        if (!HttpHelper::isValidHeaderValue($value, $this->valueCharset)) {
            throw new InvalidHeaderValueException("Header value contains invalid characters", $name);
        }

        $length = match ($this->valueCharset) {
            Charset::ASCII => strlen($value),
            Charset::UTF8 => mb_strlen($value, "UTF-8"),
        };

        if ($length > $this->valueMaxLength) {
            if (!$this->valueOverflowTrim) {
                throw new InvalidHeaderValueException("Header value exceeds maximum length", $name);
            }

            return match ($this->valueCharset) {
                Charset::ASCII => substr($value, 0, $this->valueMaxLength),
                Charset::UTF8 => mb_substr($value, 0, $this->valueMaxLength, "UTF-8"),
            };
        }

        return $value;
    }
}

