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

/**
 * Class Headers
 * @package Charcoal\Http\Commons
 */
class Headers extends AbstractDataStore
{
    /**
     * @param array $data
     * @param bool $validateKeys
     * @param bool $sanitizeValues
     */
    public function __construct(array $data = [], bool $validateKeys = true, public bool $sanitizeValues = true)
    {
        parent::__construct('/^[\w\-.]+$/', $validateKeys);
        if ($data) {
            foreach ($data as $key => $value) {
                $this->setHeader($key, $value);
            }
        }
    }

    /**
     * Sets an HTTP header value key/pair
     * @param string $key
     * @param string $value
     * @return void
     */
    protected function setHeader(string $key, string $value): void
    {
        if ($this->sanitizeValues) {
            // Sanitize header value
            $value = filter_var(
                trim($value),
                FILTER_UNSAFE_RAW,
                FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH
            );
        }

        if ($value) {
            $this->storeKeyValue(new KeyValuePair($key, $value));
        }
    }

    /**
     * Returns HTTP header value as string or NULL if it has not been set
     * @param string $key
     * @return string|null
     */
    public function get(string $key): ?string
    {
        return $this->getValue($key)?->value;
    }
}

