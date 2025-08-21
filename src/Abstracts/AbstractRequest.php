<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Abstracts;

use Charcoal\Http\Commons\Enums\HttpMethod;
use Charcoal\Http\Commons\Enums\HttpProtocol;
use Charcoal\Http\Commons\Header\Headers;

/**
 * Represents an abstract HTTP request.
 * This class serves as a base for handling HTTP requests, encapsulating
 * essential information such as the protocol, method, and headers.
 * It is intended to be extended by concrete implementations that define
 * the specifics of handling particular types of requests.
 * @noinspection PhpClassCanBeReadonlyInspection
 */
abstract class AbstractRequest
{
    public function __construct(
        public readonly HttpProtocol $protocol,
        public readonly HttpMethod   $method,
        public readonly Headers      $headers,
    )
    {
    }
}