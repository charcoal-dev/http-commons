<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Enums\Security;

use Charcoal\Http\Commons\Contracts\CredentialEncodingFormatInterface;

/**
 * Class CredentialEncoding
 * @package Charcoal\Http\Commons\Enums\Security
 */
enum CredentialEncoding implements CredentialEncodingFormatInterface
{
    case PEM;
    case DER;
}