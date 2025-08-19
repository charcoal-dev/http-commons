<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Body;

use Charcoal\Base\Abstracts\AbstractDataset;
use Charcoal\Base\Abstracts\Dataset\BatchEnvelope;
use Charcoal\Base\Abstracts\Dataset\KeyValue;
use Charcoal\Base\Contracts\Charsets\UnicodeLanguageRangeInterface;
use Charcoal\Base\Enums\Charset;

/**
 * Class Payload
 * @package Charcoal\Http\Commons\Body
 * @template-extends AbstractDataset<KeyValue<string,mixed>,int|string|float|bool|null|array>
 */
class Payload extends AbstractDataset
{
    /** @var UnicodeLanguageRangeInterface[] */
    protected array $unicodeRanges = [];

    /**
     * @param BatchEnvelope|null $seed
     * @throws \Charcoal\Base\Exceptions\WrappedException
     */
    public function __construct(?BatchEnvelope $seed = null)
    {
        parent::__construct(Charset::UTF8, $seed);
    }

    /**
     * @param UnicodeLanguageRangeInterface ...$ranges
     * @return void
     */
    public function setUnicodeRanges(UnicodeLanguageRangeInterface ...$ranges): void
    {
        $this->unicodeRanges = $ranges;
    }
}