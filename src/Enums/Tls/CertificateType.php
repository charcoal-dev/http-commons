<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Enums\Tls;

/**
 * Class CertificateType
 * @package Charcoal\Http\Commons\Enums\Tls
 */
enum CertificateType
{
    case Certificate;
    case PrivateKey;
    case PublicKey;
    case CertificateRequest;
}