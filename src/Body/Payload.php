<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Body;

use Charcoal\Base\Abstracts\Dataset\ValidatingDataset;
use Charcoal\Base\Contracts\Charsets\UnicodeLanguageRangeInterface;
use Charcoal\Base\Contracts\Vectors\StringVectorInterface;
use Charcoal\Base\Support\Data\BatchEnvelope;
use Charcoal\Base\Support\Data\CheckedKeyValue;
use Charcoal\Http\Commons\Data\HttpDataPolicy;
use Charcoal\Http\Commons\Enums\HttpParamKeyPolicy;
use Charcoal\Http\Commons\Exception\InvalidParamKeyException;
use Charcoal\Http\Commons\Exception\InvalidParamValueException;
use Charcoal\Http\Commons\Support\HttpHelper;

/**
 * Class Payload
 * @package Charcoal\Http\Commons\Body
 * @property HttpDataPolicy $policy
 * @template-extends ValidatingDataset<CheckedKeyValue<string,int|string|float|bool|null|array>>
 */
class Payload extends ValidatingDataset
{
    /** @var UnicodeLanguageRangeInterface[] */
    protected array $unicodeRanges = [];

    /**
     * @param HttpDataPolicy $dataPolicy
     * @param HttpParamKeyPolicy $keyPolicy
     * @param BatchEnvelope|null $seed
     * @throws \Charcoal\Base\Exceptions\WrappedException
     */
    public function __construct(
        HttpDataPolicy                     $dataPolicy,
        public readonly HttpParamKeyPolicy $keyPolicy = HttpParamKeyPolicy::STRICT,
        ?BatchEnvelope                     $seed = null,
    )
    {
        parent::__construct($dataPolicy, $seed);
    }

    /**
     * @param UnicodeLanguageRangeInterface ...$ranges
     * @return void
     */
    public function setUnicodeRanges(UnicodeLanguageRangeInterface ...$ranges): void
    {
        $this->unicodeRanges = $ranges;
    }

    /**
     * @param string $key
     * @return string
     * @throws InvalidParamKeyException
     */
    protected function validateEntryKey(string $key): string
    {
        if (!HttpHelper::isValidParamKey($key, $this->keyPolicy)) {
            throw new InvalidParamKeyException("Encountered invalid payload param key", $key);
        }

        if (strlen($key) > $this->policy->keyMaxLength) {
            if (!$this->policy->keyOverflowTrim) {
                throw new InvalidParamKeyException("Payload param keys exceeds maximum length", $key);
            }

            return substr($key, 0, $this->policy->keyMaxLength);
        }

        return $key;
    }

    /**
     * @param mixed $value
     * @param string $key
     * @return int|string|float|bool|array|null
     * @throws InvalidParamValueException
     */
    protected function validateEntryValue(mixed $value, string $key): int|string|float|bool|null|array
    {
        if (is_scalar($value) || is_null($value)) {
            return $value;
        }

        if (is_array($value) || is_object($value)) {
            $fromObject = null;
            if (is_object($value)) {
                $fromObject = match (true) {
                    $value instanceof \DateTimeInterface => $value->format(DATE_ATOM),
                    $value instanceof \BackedEnum => $value->value,
                    $value instanceof \UnitEnum => $value->name,
                    $value instanceof \Stringable => (string)$value,
                    $value instanceof StringVectorInterface => $value->getArray(),
                    default => null,
                };
            }

            if ($fromObject !== null) {
                return $fromObject;
            }

            try {
                return json_decode(json_encode($value, JSON_THROW_ON_ERROR),
                    true, flags: JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new InvalidParamValueException(sprintf(
                    'Cannot set %s as HTTP payload param "%s"', (is_object($value) ?
                    sprintf('object of type "%s"', get_class($value)) : "Array"
                ), $key), $key, previous: $e);
            }
        }

        throw new InvalidParamValueException(sprintf(
            'Cannot set "%s" as HTTP payload param "%s"', get_debug_type($value), $key), $key);
    }
}