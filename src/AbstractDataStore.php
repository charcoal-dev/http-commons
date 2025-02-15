<?php
/*
 * This file is a part of "charcoal-dev/http-commons" package.
 * https://github.com/charcoal-dev/http-commons
 *
 * Copyright (c) Furqan A. Siddiqui <hello@furqansiddiqui.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit following link:
 * https://github.com/charcoal-dev/http-commons/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons;

/**
 * Class AbstractDataStore
 * @package Charcoal\Http\Commons
 */
abstract class AbstractDataStore implements \IteratorAggregate
{
    protected array $data = [];
    protected int $count = 0;

    /**
     * @param string $validateKeyRegExp
     * @param bool $validateKeys
     */
    protected function __construct(
        protected readonly string $validateKeyRegExp,
        public bool               $validateKeys = true,
    )
    {
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists(strtolower($key), $this->data);
    }

    /**
     * @return array
     */
    final public function toArray(): array
    {
        $data = [];
        /** @var \Charcoal\Http\Commons\KeyValuePair $prop */
        foreach ($this->data as $prop) {
            $data[$prop->key] = $prop->value;
        }

        return $data;
    }

    /**
     * @param \Charcoal\Http\Commons\KeyValuePair $pair
     * @return bool
     */
    protected function storeKeyValue(KeyValuePair $pair): bool
    {
        if ($this->validateKeys && !preg_match($this->validateKeyRegExp, $pair->key)) {
            return false;
        }

        $this->data[strtolower($pair->key)] = $pair;
        $this->count++;
        return true;
    }

    /**
     * @param string $key
     * @return \Charcoal\Http\Commons\KeyValuePair|null
     */
    protected function getValue(string $key): ?KeyValuePair
    {
        return $this->data[strtolower($key)] ?? null;
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function hasValue(string $key): bool
    {
        return isset($this->data[strtolower($key)]);
    }

    /**
     * @param string $key
     * @return void
     */
    protected function deleteValue(string $key): void
    {
        $key = strtolower($key);
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
            $this->count--;
        }
    }

    /**
     * @return \Traversable
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->data);
    }
}
