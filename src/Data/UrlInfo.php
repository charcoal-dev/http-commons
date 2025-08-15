<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Data;

use Charcoal\Http\Commons\Exceptions\InvalidUrlException;

/**
 * Class UrlInfo
 * @package Charcoal\Http\Commons
 */
readonly class UrlInfo
{
    public string $complete;
    public ?string $scheme;
    public ?string $host;
    public ?int $port;
    public ?string $username;
    public ?string $password;
    public ?string $path;
    public ?string $query;
    public ?string $fragment;

    /**
     * @param string $url
     * @throws InvalidUrlException
     */
    public function __construct(string $url)
    {
        $parsed = parse_url($url);
        if (!is_array($parsed) || !$parsed) {
            throw new InvalidUrlException("Invalid URL");
        }

        $this->complete = $url;
        $this->scheme = $parsed["scheme"] ?? null;
        $this->host = $parsed["host"] ?? null;
        $this->port = $parsed["port"] ?? null;
        $this->username = $parsed["user"] ?? null;
        $this->password = $parsed["pass"] ?? null;
        $this->path = $parsed["path"] ?? null;
        $this->query = $parsed["query"] ?? null;
        $this->fragment = $parsed["fragment"] ?? null;
    }
}
