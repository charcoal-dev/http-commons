<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Exception;

/**
 * Class InvalidHeaderNameException
 * @package Charcoal\Http\Commons\Exception
 */
class InvalidHeaderNameException extends HeaderException implements HttpDataAppendExceptionInterface
{
    public function __construct(
        string                 $message,
        public readonly string $header,
        int                    $code = 0,
        \Throwable             $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}