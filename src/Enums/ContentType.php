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
    case TEXT = "text/plain";
    case HTML = "text/html";
    case JSON = "application/json";

    /**
     * @param string $header
     * @return self|null
     */
    public static function find(string $header): ?self
    {
        return match (strtolower(trim(explode(";", $header)[0]))) {
            "text/plain" => self::TEXT,
            "text/html" => self::HTML,
            "application/json" => self::JSON,
            default => null,
        };
    }
}