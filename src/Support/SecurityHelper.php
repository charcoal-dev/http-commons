<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Support;

use Charcoal\Http\Commons\Enums\Security\CredentialType;

/**
 * Class TlsHelper
 * @package Charcoal\Http\Commons\Support
 */
class SecurityHelper
{
    /**
     * @param string $contents
     * @return false|CredentialType
     */
    public static function checkPemCredential(string $contents): false|CredentialType
    {
        if (preg_match('/^-+BEGIN [A-Z0-9 ]+-+\r?\n[0-9A-Za-z+\/=\r\n]+^-+END [A-Z0-9 ]+-+$/m', $contents)) {
            if (preg_match('/^-+BEGIN ([A-Z0-9 ]+)-+\r?\n/', $contents, $type)) {
                $type = strtolower(trim($type[1] ?? ""));
                return match ($type) {
                    "certificate" => CredentialType::Certificate,
                    "certificate request" => CredentialType::CertificateRequest,
                    default => match (true) {
                        str_ends_with($type, "private key") => CredentialType::PrivateKey,
                        default => false,
                    },
                };
            }
        }

        return false;
    }
}