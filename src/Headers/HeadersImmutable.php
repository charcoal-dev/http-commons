<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Headers;

use Charcoal\Base\Abstracts\Dataset\KeyValue;
use Charcoal\Http\Commons\Contracts\HeadersInterface;

/**
 * Represents an immutable collection of HTTP headers.
 * This class provides read-only access to HTTP header data and ensures
 * that the headers cannot be modified after being instantiated.
 */
final readonly class HeadersImmutable implements HeadersInterface, \IteratorAggregate, \Countable
{
    /** @var array<string,KeyValue<string,string> */
    public array $dataset;
    public int $count;

    public function __construct(Headers $headers)
    {
        $this->dataset = $headers->getDataset();
        $this->count = count($this->dataset);
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function get(string $name): ?string
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