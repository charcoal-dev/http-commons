<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Headers;

use Charcoal\Base\Abstracts\AbstractDataset;
use Charcoal\Base\Abstracts\Dataset\BatchEnvelope;
use Charcoal\Base\Abstracts\Dataset\KeyValue;
use Charcoal\Base\Enums\Charset;
use Charcoal\Http\Commons\Contracts\HeadersInterface;

/**
 * Represents a collection of headers that can be manipulated and converted to an immutable state.
 * @template-extends AbstractDataset<KeyValue<string, string>,string>
 */
final class Headers extends AbstractDataset implements HeadersInterface
{
    /**
     * @param BatchEnvelope|null $headers
     * @throws \Charcoal\Base\Exceptions\WrappedException
     */
    public function __construct(?BatchEnvelope $headers = null)
    {
        parent::__construct(Charset::ASCII, $headers);
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    final public function set(string $name, string $value): self
    {
        return $this->storeEntry($name, $value);
    }

    /**
     * @param string $name
     * @return $this
     */
    final public function delete(string $name): self
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

    /**
     * @param string $name
     * @return string|null
     */
    public function get(string $name): ?string
    {
        return $this->getEntry($name)?->value;
    }

    /**
     * @return HeadersImmutable
     */
    public function toImmutable(): HeadersImmutable
    {
        return new HeadersImmutable($this);
    }
}

