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
use Charcoal\Http\Commons\Data\HttpDataPolicy;
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
        HttpDataPolicy                      $dataPolicy,
        ValidationState                     $accessTrust = ValidationState::RAW,
    )
    {
        parent::__construct($dataPolicy, $accessTrust);
    }

    /**
     * @throws InvalidHeaderNameException
     */
    protected function validateEntityKeyFn(string $name): string
    {
        if (!HttpHelper::isValidHeaderName($name, $this->keyPolicy)) {
            throw new InvalidHeaderNameException("Encountered invalid header name", $name);
        }

        if (strlen($name) > $this->policy->keyMaxLength) {
            if (!$this->policy->keyOverflowTrim) {
                throw new InvalidHeaderNameException("Header name exceeds maximum length", $name);
            }

            return substr($name, 0, $this->policy->keyMaxLength);
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

        if (!HttpHelper::isValidHeaderValue($value, $this->policy->valueCharset)) {
            throw new InvalidHeaderValueException("Header value contains invalid characters", $name);
        }

        $length = match ($this->policy->valueCharset) {
            Charset::ASCII => strlen($value),
            Charset::UTF8 => mb_strlen($value, "UTF-8"),
        };

        if ($length > $this->policy->valueMaxLength) {
            if (!$this->policy->valueOverflowTrim) {
                throw new InvalidHeaderValueException("Header value exceeds maximum length", $name);
            }

            return match ($this->policy->valueCharset) {
                Charset::ASCII => substr($value, 0, $this->policy->valueMaxLength),
                Charset::UTF8 => mb_substr($value, 0, $this->policy->valueMaxLength, "UTF-8"),
            };
        }

        return $value;
    }
}

