<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Support;

/**
 * Defines a Cross-Origin Resource Sharing (CORS) policy.
 * This class represents the configuration for managing CORS requests, specifying
 * allowed origins, HTTP methods, maximum age for preflight requests, and whether
 * credentials are included in cross-origin requests.
 */
final readonly class CorsPolicy
{
    public array $origins;
    public string $methods;

    /**
     * Constructor for initializing CORS settings.
     */
    public function __construct(
        ?array        $origins,
        HttpMethods   $methods,
        public string $allow = "Content-Type, Content-Length, Authorization",
        public string $expose = "Location, ETag, Retry-After, Content-Disposition, Content-Transfer-Encoding",
        public int    $maxAge = 600,
        public bool   $withCredentials = false,
    )
    {
        $final = [];
        foreach ($origins as $origin) {
            if (!HttpHelper::isValidOrigin($origin)) {
                throw new \InvalidArgumentException("Invalid CORS origin: " . $origin);
            }

            $final[] = strtolower($origin);
        }

        $this->origins = $final;
        if (!$this->origins && $this->withCredentials) {
            throw new \LogicException("Credentials are not allowed without an origin");
        }

        $this->methods = $methods->count() ? implode(", ", array_map(fn($m) => $m->name, $methods->getArray())) : "*";
        if ($this->maxAge < 0) {
            throw new \OutOfRangeException("Max age must be a non-negative integer");
        }
    }
}