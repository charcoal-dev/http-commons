<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Support;

use Charcoal\Http\Commons\Enums\HttpMethod;
use Charcoal\Vectors\Enums\AbstractEnumVector;

/**
 * Represents a collection of HTTP methods.
 * Extends the AbstractEnumVector class to manage enumerated HTTPMethod objects.
 * @implements \IteratorAggregate<HttpMethod>
 * @extends AbstractEnumVector<HttpMethod>
 */
final class HttpMethods extends AbstractEnumVector
{
    public function __construct(HttpMethod ...$methods)
    {
        parent::__construct(...$methods);
    }
}