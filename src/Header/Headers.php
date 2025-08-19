<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Header;

use Charcoal\Base\Abstracts\AbstractDataset;
use Charcoal\Base\Abstracts\Dataset\BatchEnvelope;
use Charcoal\Base\Abstracts\Dataset\KeyValue;
use Charcoal\Base\Enums\Charset;

/**
 * Class Headers
 * @package Charcoal\Http\Commons\Header
 * @template-extends AbstractDataset<KeyValue<string, string>,string>
 */
class Headers extends AbstractDataset
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
     * @return string|null
     */
    public function get(string $name): ?string
    {
        return $this->getEntry($name)?->value;
    }
}

