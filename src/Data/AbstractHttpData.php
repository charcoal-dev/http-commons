<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Data;

use Charcoal\Base\Enums\ValidationState;

/**
 * Class AbstractHttpData
 * @package Charcoal\Http\Commons\Data
 * @property array<string,KeyValuePair> $data
 */
abstract class AbstractHttpData implements \IteratorAggregate
{
    /** @var array<string,KeyValuePair> $data */
    protected array $data = [];

    /**
     * @param HttpDataPolicy $policy
     * @param ValidationState $accessTrust
     */
    public function __construct(
        public readonly HttpDataPolicy $policy,
        protected ValidationState      $accessTrust = ValidationState::RAW,
    )
    {
    }

    abstract protected function validateEntityKeyFn(string $name): string;

    abstract protected function validateEntityValueFn(mixed $value, string $name): int|string|float|bool|null|array;

    /**
     * @param ValidationState $trust
     * @return $this
     */
    public function setAccessTrust(ValidationState $trust): static
    {
        $this->accessTrust = $trust;
        return $this;
    }

    /**
     * @param string $key
     * @param ValidationState $trust
     * @return string
     */
    protected function validateEntityKey(string $key, ValidationState $trust): string
    {
        return $trust->value < ValidationState::VALIDATED->value ?
            $this->validateEntityKeyFn($key) : $key;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function normalizeEntityKey(string $key): string
    {
        return strtolower($key);
    }

    /**
     * @param string $key
     * @param int|string|float|bool|array|null $value
     * @return $this
     */
    protected function storeKeyValue(string $key, int|string|float|bool|null|array $value): static
    {
        $key = $this->validateEntityKey(trim($key), $this->policy->trust);
        $indexId = $this->normalizeEntityKey($key);
        if ($this->policy->trust->value < ValidationState::VALIDATED->value) {
            $value = $this->validateEntityValueFn($value, $key);
        }

        $this->data[$indexId] = new KeyValuePair($key, $value);
        return $this;
    }

    /**
     * @param string $key
     * @return string
     */
    final protected function accessKey(string $key): string
    {
        return $this->normalizeEntityKey($this->validateEntityKey(trim($key), $this->accessTrust));
    }

    /**
     * @param string $key
     * @return KeyValuePair|null
     */
    protected function getKeyValue(string $key): ?KeyValuePair
    {
        return $this->data[$this->accessKey($key)] ?? null;
    }

    /**
     * @param string $key
     * @return $this
     */
    protected function deleteKeyValue(string $key): static
    {
        $key = $this->accessKey($key);
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
        }

        return $this;
    }

    /**
     * @param string $key
     * @return bool
     */
    final public function has(string $key): bool
    {
        return array_key_exists($this->accessKey($key), $this->data);
    }

    /**
     * @return int
     */
    final public function count(): int
    {
        return count($this->data);
    }

    /**
     * @return array
     */
    final public function toArray(): array
    {
        $data = [];
        foreach ($this->data as $prop) {
            $data[$prop->key] = $prop->value;
        }

        return $data;
    }

    /**
     * @return \Traversable
     */
    final public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->data);
    }
}
