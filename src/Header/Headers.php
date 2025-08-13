<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Header;

use Charcoal\Base\Abstracts\Dataset\ValidatingDataset;
use Charcoal\Base\Enums\Charset;
use Charcoal\Base\Support\Data\BatchEnvelope;
use Charcoal\Base\Support\Data\CheckedKeyValue;
use Charcoal\Http\Commons\Data\HttpDataPolicy;
use Charcoal\Http\Commons\Enums\HttpHeaderKeyPolicy;
use Charcoal\Http\Commons\Exception\InvalidHeaderNameException;
use Charcoal\Http\Commons\Exception\InvalidHeaderValueException;
use Charcoal\Http\Commons\Support\HttpHelper;

/**
 * Class Headers
 * @package Charcoal\Http\Commons\Header
 * @property HttpDataPolicy $policy
 * @template-extends ValidatingDataset<CheckedKeyValue<string, string>>
 */
class Headers extends ValidatingDataset
{
    /**
     * @param HttpDataPolicy $dataPolicy
     * @param HttpHeaderKeyPolicy $keyPolicy
     * @param BatchEnvelope|null $headers
     * @throws \Charcoal\Base\Exceptions\WrappedException
     */
    public function __construct(
        HttpDataPolicy                      $dataPolicy,
        public readonly HttpHeaderKeyPolicy $keyPolicy = HttpHeaderKeyPolicy::STRICT,
        ?BatchEnvelope                      $headers = null,
    )
    {
        parent::__construct($dataPolicy, $headers);
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function get(string $name): ?string
    {
        return $this->getEntry($name)?->value;
    }

    /**
     * @param string $key
     * @return string
     * @throws InvalidHeaderNameException
     */
    protected function validateEntryKey(string $key): string
    {
        if (!HttpHelper::isValidHeaderName($key, $this->keyPolicy)) {
            throw new InvalidHeaderNameException("Encountered invalid header name", $key);
        }

        if (strlen($key) > $this->policy->keyMaxLength) {
            if (!$this->policy->keyOverflowTrim) {
                throw new InvalidHeaderNameException("Header name exceeds maximum length", $key);
            }

            return substr($key, 0, $this->policy->keyMaxLength);
        }

        return $key;
    }

    /**
     * @throws InvalidHeaderValueException
     */
    protected function validateEntryValue(mixed $value, string $key): string
    {
        if (!is_string($value)) {
            throw new InvalidHeaderValueException("Header value must be a string", $key);
        }

        if (!HttpHelper::isValidHeaderValue($value, Charset::ASCII)) {
            throw new InvalidHeaderValueException("Header value contains invalid characters", $key);
        }

        $length = $this->policy->strlen($value);
        if ($length > $this->policy->valueMaxLength) {
            if (!$this->policy->valueOverflowTrim) {
                throw new InvalidHeaderValueException("Header value exceeds maximum length", $key);
            }

            return $this->policy->cutToSize($value, $this->policy->valueMaxLength);
        }

        return $value;
    }
}

