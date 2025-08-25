<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Contracts;

/**
 * Represents a contract for accessing and managing a structured collection of data.
 */
interface PayloadInterface
{
    public function get(string $name): int|string|float|bool|null|array;

    public function has(string $name): bool;

    public function count(): int;

    public function getIterator(): \Traversable;
}