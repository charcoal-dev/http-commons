<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Data;

use Charcoal\Base\Abstracts\Dataset\DatasetPolicy;
use Charcoal\Base\Abstracts\Dataset\DatasetStorageMode;
use Charcoal\Base\Enums\Charset;
use Charcoal\Base\Enums\ValidationState;

/**
 * Class HttpDataPolicy
 * @package Charcoal\Http\Commons\Data
 */
readonly class HttpDataPolicy extends DatasetPolicy
{
    public function __construct(
        public Charset  $charset,
        public int      $keyMaxLength = 64,
        public bool     $keyOverflowTrim = false,
        public int      $valueMaxLength = 2048,
        public bool     $valueOverflowTrim = false,
        ValidationState $accessKeyTrust = ValidationState::VALIDATED,
        ValidationState $setterKeyTrust = ValidationState::RAW,
        ValidationState $valueTrust = ValidationState::RAW,
    )
    {
        parent::__construct(DatasetStorageMode::ENTRY_OBJECTS,
            $accessKeyTrust, $setterKeyTrust, $valueTrust);
    }

    /**
     * @param string $value
     * @return int
     */
    public function strlen(string $value): int
    {
        return match ($this->charset) {
            Charset::ASCII => strlen($value),
            Charset::UTF8 => mb_strlen($value, "UTF-8"),
        };
    }

    /**
     * @param string $value
     * @param int $length
     * @return string
     */
    public function cutToSize(string $value, int $length): string
    {
        return match ($this->charset) {
            Charset::ASCII => substr($value, 0, $length),
            Charset::UTF8 => mb_substr($value, 0, $length, "UTF-8"),
        };
    }
}