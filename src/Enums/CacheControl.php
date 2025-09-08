<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Enums;

use Charcoal\Http\Commons\Support\CacheControlDirectives;

/**
 * An enumeration representing HTTP cache control directives.
 * This enum provides three cache control options: Public, Private, and NoStore.
 * It also offers a method to generate the appropriate directives based on the selected cache
 * control option and additional parameters.
 */
enum CacheControl: string
{
    case Public = "public";
    case Private = "private";
    case NoStore = "no-store";

    /**
     * Retrieves the cache directives for public CDN caching.
     */
    public static function forPublicCdn(int $maxAge, int $sMaxAge = 0, bool $immutable = false): CacheControlDirectives
    {
        return new CacheControlDirectives(self::Public, $maxAge, immutable: $immutable, sMaxAge: $sMaxAge);
    }

    /**
     * Retrieves the cache directives for client browser caching.
     */
    public static function forClientBrowser(int $maxAge, bool $mustRevalidate = false): CacheControlDirectives
    {
        return new CacheControlDirectives(self::Private, $maxAge, mustRevalidate: $mustRevalidate);
    }

    /**
     * Retrieves the cache directives for server caching.
     */
    public static function forServerCached(): CacheControlDirectives
    {
        return new CacheControlDirectives(self::NoStore);
    }
}