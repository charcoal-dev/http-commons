<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Contracts;

/**
 * Interface defining methods for interacting with HTTP headers.
 */
interface HeadersInterface
{
    public function get(string $name): ?string;

    public function has(string $name): bool;

    public function count(): int;

    public function getIterator(): \Traversable;
}