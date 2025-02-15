<?php
/*
 * This file is a part of "charcoal-dev/http-commons" package.
 * https://github.com/charcoal-dev/http-commons
 *
 * Copyright (c) Furqan A. Siddiqui <hello@furqansiddiqui.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit following link:
 * https://github.com/charcoal-dev/http-commons/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons;

use Charcoal\Buffers\AbstractByteArray;

/**
 * Class AbstractPayload
 * @package Charcoal\Http\Commons
 */
abstract class AbstractPayload extends AbstractDataStore
{
    /**
     * @param array $data
     * @param bool $validateKeys
     */
    public function __construct(array $data = [], bool $validateKeys = true)
    {
        parent::__construct('/^[\w\-+.]+$/', $validateKeys);
        if ($data) {
            foreach ($data as $key => $value) {
                try {
                    $this->setPayload($key, $value);
                } catch (\Exception) { // Silently discard any bad key/value pair
                }
            }
        }
    }

    /**
     * Sets a payload key/value pair
     * @param int|string $key
     * @param mixed $value
     * @return void
     */
    protected function setPayload(int|string $key, mixed $value): void
    {
        if (is_int($key)) {
            $key = strval($key);
        }

        if (is_scalar($value) || is_null($value)) {
            $pair = new KeyValuePair($key, $value);
        } elseif (is_array($value) || is_object($value)) {
            if ($value instanceof AbstractByteArray) {
                $filtered = "0x" . $value->toBase16();
            } else {
                try {
                    $filtered = json_decode(
                        json_encode($value, JSON_THROW_ON_ERROR),
                        true,
                        flags: JSON_THROW_ON_ERROR
                    );
                } catch (\JsonException) {
                    throw new \UnexpectedValueException(
                        sprintf('JSON filter failed on payload prop "%s" of type "%s"', $key, gettype($value))
                    );
                }
            }

            $pair = new KeyValuePair($key, $filtered);
        }

        if (!isset($pair)) {
            throw new \InvalidArgumentException(
                sprintf('Cannot set Http Payload value for "%s" of type "%s"', $key, gettype($value))
            );
        }

        $this->storeKeyValue($pair);
    }
}
