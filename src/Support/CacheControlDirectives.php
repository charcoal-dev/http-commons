<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Support;

use Charcoal\Http\Commons\Enums\CacheControl;

/**
 * A class responsible for constructing HTTP cache control directives.
 * This class generates an array of cache control directives based on the provided
 * parameters. It can handle various directives such as "no-store", "max-age",
 * "immutable", "must-revalidate", "no-transform", and "s-maxage".
 */
final readonly class CacheControlDirectives
{
    public array $directives;

    public function __construct(
        CacheControl $store,
        int          $maxAge = 0,
        bool         $immutable = false,
        bool         $mustRevalidate = false,
        bool         $noTransform = false,
        int          $sMaxAge = 0,
    )
    {
        if ($store === CacheControl::NoStore) {
            $this->directives = ["no-store", "no-cache", "must-revalidate"];
            return;
        }

        $directives = [$store->value];
        $directives[] = $maxAge > 0 ? "max-age=" . $maxAge : "no-cache";
        if ($sMaxAge > 0 && $maxAge > 0) $directives[] = "s-maxage=" . $sMaxAge;
        if ($immutable) $directives[] = "immutable";
        if ($mustRevalidate) $directives[] = "must-revalidate";
        if ($noTransform) $directives[] = "no-transform";

        $this->directives = $directives;
    }
}