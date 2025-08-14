<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Enums\Security;

/**
 * Class CredentialType
 * @package Charcoal\Http\Commons\Enums\Security
 */
enum CredentialType
{
    case Certificate;
    case PrivateKey;
    case PublicKey;
    case CertificateRequest;
}