<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Support;

use Charcoal\Http\Commons\Enums\Tls\CertificateType;

/**
 * Class TlsHelper
 * @package Charcoal\Http\Commons\Support
 */
class TlsHelper
{
    /**
     * @param string $contents
     * @return false|CertificateType
     */
    public static function checkPemCertificate(string $contents): false|CertificateType
    {
        if (preg_match('/^-+BEGIN [A-Z0-9 ]+-+\r?\n[0-9A-Za-z+\/=\r\n]+^-+END [A-Z0-9 ]+-+$/m', $contents)) {
            if (preg_match('/^-+BEGIN ([A-Z0-9 ]+)-+\r?\n/', $contents, $type)) {
                $type = strtolower(trim($type[1] ?? ""));
                return match ($type) {
                    "certificate" => CertificateType::Certificate,
                    "certificate request" => CertificateType::CertificateRequest,
                    default => match (true) {
                        str_ends_with($type, "private key") => CertificateType::PrivateKey,
                        default => false,
                    },
                };
            }
        }

        return false;
    }
}