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
readonly class CacheControlDirectives
{
    public array $directives;

    public function __construct(
        CacheControl $store,
        int          $maxAge = 0,
        bool         $noCache = false,
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
        $directives[] = "max-age=" . max(0, $maxAge);
        if ($sMaxAge > 0) $directives[] = "s-maxage=" . $sMaxAge;
        if ($noCache) $directives[] = "no-cache";
        if ($immutable) $directives[] = "immutable";
        if ($mustRevalidate) $directives[] = "must-revalidate";
        if ($noTransform) $directives[] = "no-transform";

        $this->directives = $directives;
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        if (in_array("no-store", $this->directives, true)) {
            return $this->directives === ["no-store", "no-cache", "must-revalidate"];
        }

        $hasImmutable = in_array("immutable", $this->directives, true);
        $hasNoCache   = in_array("no-cache", $this->directives, true);

        if ($hasImmutable) {
            foreach ($this->directives as $d) {
                if (str_starts_with($d, "max-age=")) {
                    $age = (int)substr($d, 8);
                    if ($age <= 0) {
                        return false;
                    }
                }
            }
        }

        if ($hasImmutable && $hasNoCache) {
            return false;
        }

        return true;
    }
}