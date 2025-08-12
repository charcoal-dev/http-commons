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

    /**
     * @param int $keyMaxLength
     * @param bool $keyOverflowTrim
     * @param Charset $valueCharset
     * @param int $valueMaxLength
     * @param bool $valueOverflowTrim
     * @param ValidationState $writeTrust
     * @param ValidationState $accessTrust
     * @param bool $countLengthUtf8
     */
    public function __construct(
        public readonly int             $keyMaxLength,
        public readonly bool            $keyOverflowTrim,
        public readonly Charset         $valueCharset,
        public readonly int             $valueMaxLength,
        public readonly bool            $valueOverflowTrim,
        public readonly ValidationState $writeTrust = ValidationState::RAW,
        protected ValidationState       $accessTrust = ValidationState::RAW,
        protected bool                  $countLengthUtf8 = false,
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
        $key = $this->validateEntityKey($key, $this->writeTrust);
        $indexId = $this->normalizeEntityKey($key, $this->writeTrust);
        if ($this->writeTrust->value < ValidationState::VALIDATED->value) {
            $value = $this->validateEntityValueFn($value, $key);
        }

        $this->data[$indexId] = new KeyValuePair($key, $value);
        return $this;
    }

    /**
     * @param string $key
     * @return string
     */
    final protected function indexKey(string $key): string
    {
        return $this->normalizeEntityKey($this->validateEntityKey($key, $this->accessTrust), $this->accessTrust);
    }

    /**
     * @param string $key
     * @return KeyValuePair|null
     */
    final public function get(string $key): ?KeyValuePair
    {
        return $this->data[$this->indexKey($key)] ?? null;
    }

    /**
     * @param string $key
     * @return $this
     */
    protected function deleteKeyValue(string $key): static
    {
        $key = $this->indexKey($key);
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
        return array_key_exists($this->indexKey($key), $this->data);
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

    /**
     * @param string $value
     * @return int
     */
    protected function calcLength(string $value): int
    {
        if ($this->valueCharset === Charset::UTF8 && $this->countLengthUtf8) {
            return mb_strlen($value, "UTF-8");
        }

        return strlen($value);
    }
}
