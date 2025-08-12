<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons;

/**
 * Class AbstractHttpData
 * @package Charcoal\Http\Commons
 * @property array<string,KeyValuePair> $data
 */
abstract class AbstractHttpData implements \IteratorAggregate
{
    /** @var array<string,KeyValuePair> $data */
    protected array $data = [];
    protected int $count = 0;

    abstract protected function sanitizeStoreKey(string $key): string;

    protected function normalizeSanitizeStoreKey(string $key): string
    {
        return strtolower(trim($this->sanitizeStoreKey($key)));
    }

    public function count(): int
    {
        return $this->count;
    }

    public function has(string $key): bool
    {
        return array_key_exists($this->normalizeSanitizeStoreKey($key), $this->data);
    }

    protected function storeKeyValue(KeyValuePair $pair): bool
    {
        $this->data[$this->normalizeSanitizeStoreKey($pair->key)] = $pair;
        $this->count++;
        return true;
    }

    protected function getKeyValue(string $key): ?KeyValuePair
    {
        return $this->data[$this->normalizeSanitizeStoreKey($key)] ?? null;
    }

    protected function hasValue(string $key): bool
    {
        return isset($this->data[$this->normalizeSanitizeStoreKey($key)]);
    }

    protected function deleteValue(string $key): void
    {
        $key = $this->normalizeSanitizeStoreKey($key);
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
            $this->count--;
        }
    }

    final public function toArray(): array
    {
        $data = [];
        foreach ($this->data as $prop) {
            $data[$prop->key] = $prop->value;
        }

        return $data;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->data);
    }
}
