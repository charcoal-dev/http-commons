<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Body;

use Charcoal\Http\Commons\Contracts\PayloadInterface;

/**
 * Class WritablePayload
 * @package Charcoal\Http\Commons\Body
 */
class WritablePayload extends Payload implements PayloadInterface
{
    /**
     * @param string $name
     * @return int|string|float|bool|array|null
     */
    public function get(string $name): int|string|float|bool|null|array
    {
        return $this->getEntry($name)?->value ?? null;
    }

    /**
     * @param string $name
     * @param int|string|float|bool|array|null $value
     * @return $this
     */
    final public function set(string $name, int|string|float|bool|null|array $value): static
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