<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Exception;

use Charcoal\Http\Commons\Security\CredentialFilepath;

/**
 * Class CredentialFileException
 * @package Charcoal\Http\Commons\Exception
 */
class CredentialFileException extends \Exception
{
    public function __construct(
        public readonly CredentialFilepath $object,
        string                             $message,
        int                                $code = 0,
        ?\Throwable                        $previous = null,
    )
    {
        parent::__construct($message, $code, $previous);
    }
}