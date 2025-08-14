<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Enums\Tls;

use Charcoal\Http\Commons\Contracts\CertificateEncodingFormatInterface;

/**
 * Class Encoding
 * @package Charcoal\Http\Commons\Enums\Tls
 */
enum Encoding implements CertificateEncodingFormatInterface
{
    case PEM;
    case DER;
}