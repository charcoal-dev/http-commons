<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Enums;

use Charcoal\Http\Commons\Contracts\ContentTypeEnumInterface;

/**
 * Class ContentType
 * @package Charcoal\Http\Commons\Enums
 */
enum ContentType: string implements ContentTypeEnumInterface
{
    case Text = "text/plain";
    case Html = "text/html";
    case Stylesheet = "text/css";
    case Json = "application/json";
    case Xml = "application/xml";
    case FormSubmit = "application/x-www-form-urlencoded";
    case ZipArchive = "application/zip";
    case OctetStream = "application/octet-stream";
    case Javascript = "application/javascript";
    case ImageJpeg = "image/jpeg";
    case ImageGif = "image/gif";
    case ImagePng = "image/png";
    case DocumentPdf = "application/pdf";

    public static function find(string $header): ?self
    {
        $header = strtolower(trim(explode(";", $header)[0]));
        $type = self::tryFrom($header);
        if (!$type) {
            return match ($header) {
                "text/javascript" => self::Javascript,
                default => null,
            };
        }

        return $type;
    }
}