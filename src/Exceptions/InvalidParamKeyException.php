<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Exceptions;

/**
 * Class InvalidParamKeyException
 * @package Charcoal\Http\Commons\Exceptions
 */
class InvalidParamKeyException extends \Exception
{
    public function __construct(
        string                 $message,
        public readonly string $param,
        int                    $code = 0,
        \Throwable             $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}