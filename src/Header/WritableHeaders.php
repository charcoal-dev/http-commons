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
class WritableHeaders extends Headers
{
    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    final public function set(string $name, string $value): static
    {
        return $this->storeEntry($name, $value);
    }

    /**
     * @param string $name
     * @return $this
     */
    final public function delete(string $name): static
    {
        return $this->deleteEntry($name);
    }

    /**
     * @return void
     */
    final public function flush(): void
    {
        $this->flushEntries();
    }
}