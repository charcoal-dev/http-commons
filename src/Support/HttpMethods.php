<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Support;

use Charcoal\Base\Vectors\AbstractEnumVector;
use Charcoal\Http\Commons\Enums\HttpMethod;

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