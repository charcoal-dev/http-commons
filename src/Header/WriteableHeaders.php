<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Header;

/**
 * Class WriteableHeaders
 * @package Charcoal\Http\Commons\Header
 */
class WriteableHeaders extends Headers
{
    final public function set(string $name, string $value): static
    {
        return $this->storeKeyValue($name, $value);
    }

    final public function delete(string $name): static
    {
        return $this->deleteKeyValue($name);
    }

    final public function flush(): void
    {
        $this->data = [];
    }
}