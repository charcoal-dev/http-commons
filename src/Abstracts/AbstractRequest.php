<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Abstracts;

use Charcoal\Contracts\Http\HttpRequestInterface;
use Charcoal\Http\Commons\Contracts\HeadersInterface;
use Charcoal\Http\Commons\Enums\HttpMethod;
use Charcoal\Http\Commons\Enums\HttpProtocol;

/**
 * Represents an abstract HTTP request.
 * This class serves as a base for handling HTTP requests, encapsulating
 * essential information such as the protocol, method, and headers.
 * It is intended to be extended by concrete implementations that define
 * the specifics of handling particular types of requests.
 * @noinspection PhpClassCanBeReadonlyInspection
 */
abstract class AbstractRequest implements HttpRequestInterface
{
    public function __construct(
        public readonly HttpProtocol     $protocol,
        public readonly HttpMethod       $method,
        public readonly HeadersInterface $headers,
    )
    {
    }
}