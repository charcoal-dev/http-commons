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
 * @package Charcoal\Http\Commons\Header
 */
class Headers extends AbstractHttpData
{
    /**
     * @param HttpDataPolicy $dataPolicy
     * @param HttpHeaderKeyPolicy $keyPolicy
     * @param ValidationState $accessTrust
     * @param array<string,string> $initialData
     */
    public function __construct(
        HttpDataPolicy                      $dataPolicy,
        public readonly HttpHeaderKeyPolicy $keyPolicy = HttpHeaderKeyPolicy::STRICT,
        ValidationState                     $accessTrust = ValidationState::RAW,
        array                               $initialData = [],
    )
    {
        parent::__construct($dataPolicy, $accessTrust);
        if ($initialData) {
            foreach ($initialData as $name => $value) {
                $this->storeKeyValue($name, $value);
            }
        }
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
            Charset::UTF8 => $this->policy->countLengthUtf8 ? mb_strlen($value, "UTF-8") : strlen($value),
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

