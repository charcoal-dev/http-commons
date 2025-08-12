<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Body;

use Charcoal\Base\Contracts\Vectors\StringVectorProviderInterface;
use Charcoal\Base\Enums\ExceptionAction;
use Charcoal\Base\Enums\ValidationState;
use Charcoal\Base\Vectors\AbstractTokenVector;
use Charcoal\Base\Vectors\ExceptionVector;
use Charcoal\Base\Vectors\StringVector;
use Charcoal\Buffers\AbstractByteArray;
use Charcoal\Http\Commons\Data\AbstractHttpData;
use Charcoal\Http\Commons\Data\HttpDataPolicy;
use Charcoal\Http\Commons\Enums\HttpParamKeyPolicy;
use Charcoal\Http\Commons\Exception\InvalidParamKeyException;
use Charcoal\Http\Commons\Exception\InvalidParamValueException;
use Charcoal\Http\Commons\Support\HttpHelper;

/**
 * Class Payload
 * @package Charcoal\Http\Commons\Body
 */
class Payload extends AbstractHttpData
{
    public function __construct(
        HttpDataPolicy                     $dataPolicy,
        public readonly HttpParamKeyPolicy $keyPolicy = HttpParamKeyPolicy::STRICT,
        ValidationState                    $accessTrust = ValidationState::RAW,
        array                              $initialData = [],
        ExceptionAction                    $initialDataValidation = ExceptionAction::Throw,
        ?ExceptionVector                   $exceptions = null,
    )
    {
        parent::__construct($dataPolicy, $accessTrust, $initialData, $initialDataValidation, $exceptions);
    }

    /**
     * @param string $name
     * @return int|string|float|bool|array|null
     */
    public function get(string $name): int|string|float|bool|null|array
    {
        return $this->getKeyValue($name)?->value ?? null;
    }

    /**
     * @param string $name
     * @return string
     * @throws InvalidParamKeyException
     */
    protected function validateEntityKeyFn(string $name): string
    {
        if (!HttpHelper::isValidParamKey($name, $this->keyPolicy)) {
            throw new InvalidParamKeyException("Encountered invalid payload param key", $name);
        }

        if (strlen($name) > $this->policy->keyMaxLength) {
            if (!$this->policy->keyOverflowTrim) {
                throw new InvalidParamKeyException("Payload param keys exceeds maximum length", $name);
            }

            return substr($name, 0, $this->policy->keyMaxLength);
        }

        return $name;
    }

    /**
     * @param mixed $value
     * @param string $name
     * @return int|string|float|bool|array|null
     * @throws InvalidParamValueException
     */
    protected function validateEntityValueFn(mixed $value, string $name): int|string|float|bool|null|array
    {
        if (is_scalar($value) || is_null($value)) {
            return $value;
        }

        if (is_array($value) || is_object($value)) {
            $fromObject = null;
            if (is_object($value)) {
                $fromObject = match (true) {
                    $value instanceof AbstractByteArray => "0x" . $value->toBase16(),
                    $value instanceof StringVector,
                        $value instanceof AbstractTokenVector,
                        $value instanceof StringVectorProviderInterface => $value->getArray(),
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
                ), $name), $name, previous: $e);
            }
        }

        throw new InvalidParamValueException(sprintf(
            'Cannot set "%s" as HTTP payload param "%s"', get_debug_type($value), $name), $name);
    }
}