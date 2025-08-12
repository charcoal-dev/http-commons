<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Data;

use Charcoal\Base\Enums\Charset;
use Charcoal\Base\Enums\ValidationState;
use Charcoal\Http\Commons\KeyValuePair;

/**
 * Class AbstractHttpData
 * @package Charcoal\Http\Commons
 * @property array<string,KeyValuePair> $data
 */
abstract class AbstractHttpData implements \IteratorAggregate
{
    /** @var array<string,KeyValuePair> $data */
    protected array $data = [];
    protected ?string $lastStoredIndex = null;

    /**
     * @param ValidationState $keyTrust
     * @param int $keyMaxLength
     * @param bool $keyOverflowTrim
     * @param Charset $valueCharset
     * @param ValidationState $valueTrust
     * @param int $valueMaxLength
     * @param bool $valueOverflowTrim
     */
    public function __construct(
        public readonly ValidationState $keyTrust,
        public readonly int             $keyMaxLength,
        public readonly bool            $keyOverflowTrim,
        public readonly Charset         $valueCharset,
        public readonly ValidationState $valueTrust,
        public readonly int             $valueMaxLength,
        public readonly bool            $valueOverflowTrim,
    )
    {
    }

    abstract protected function validateEntityKeyFn(string $name): string;

    abstract protected function validateEntityValueFn(mixed $value, string $name): int|string|float|bool|null|array;

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
     * @param ValidationState $trust
     * @return string
     */
    protected function normalizeEntityKey(string $key, ValidationState $trust): string
    {
        return $trust->value < ValidationState::NORMALIZED->value ?
            strtolower($key) : $key;
    }

    /**
     * @param string $key
     * @param int|string|float|bool|array|null $value
     * @return $this
     */
    protected function storeKeyValue(string $key, int|string|float|bool|null|array $value): static
    {
        $key = $this->validateEntityKey($key, $this->keyTrust);
        $indexId = $this->normalizeEntityKey($key, $this->keyTrust);
        if ($this->valueTrust->value < ValidationState::VALIDATED->value) {
            $value = $this->validateEntityValueFn($value, $key);
        }

        $this->data[$indexId] = new KeyValuePair($key, $value);
        $this->lastStoredIndex = $indexId;
        return $this;
    }

    /**
     * @param string $key
     * @param ValidationState $trust
     * @return string
     */
    final protected function indexKey(string $key, ValidationState $trust): string
    {
        return $this->normalizeEntityKey($this->validateEntityKey($key, $trust), $trust);
    }

    /**
     * @param string $key
     * @param ValidationState $trust
     * @return KeyValuePair|null
     */
    protected function getKeyValue(string $key, ValidationState $trust): ?KeyValuePair
    {
        return $this->data[$this->indexKey($key, $trust)] ?? null;
    }

    /**
     * @param string $key
     * @param ValidationState $trust
     * @return $this
     */
    protected function deleteKeyValue(string $key, ValidationState $trust): static
    {
        $key = $this->indexKey($key, $trust);
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
        }

        return $this;
    }

    /**
     * @param string $key
     * @param ValidationState $trust
     * @return bool
     */
    final public function has(string $key, ValidationState $trust): bool
    {
        return array_key_exists($this->indexKey($key, $trust), $this->data);
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
