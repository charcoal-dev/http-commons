<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Body;

use Charcoal\Base\Dataset\KeyValue;
use Charcoal\Http\Commons\Contracts\PayloadInterface;

/**
 * This class implements the PayloadInterface, \IteratorAggregate,
 * and \Countable interfaces, allowing it to be iterated over and
 * counted, while maintaining immutability.
 */
class PayloadImmutable implements PayloadInterface, \IteratorAggregate, \Countable
{
    /** @var array<string,KeyValue<string,int|string|float|bool|null|array> */
    public array $dataset;
    public int $count;

    public function __construct(Payload $payload)
    {
        $this->dataset = $payload->getDataset();
        $this->count = count($this->dataset);
    }

    /**
     * @param string $name
     * @return int|string|float|bool|array|null
     */
    public function get(string $name): int|string|float|bool|null|array
    {
        return $this->dataset[strtolower(trim($name))]?->value ?? null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->dataset[strtolower(trim($name))]);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * @return array
     */
    final public function getArray(): array
    {
        $data = [];
        foreach ($this->dataset as $prop) {
            $data[$prop->key] = $prop->value;
        }

        return $data;
    }

    /**
     * @return \Traversable<string,string>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->getArray());
    }
}