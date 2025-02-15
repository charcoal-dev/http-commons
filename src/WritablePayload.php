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
 * Class WritablePayload
 * @package Charcoal\Http\Commons
 */
class WritablePayload extends AbstractPayload
{
    /**
     * Sets a payload value
     * @param string $key
     * @param string|int|float|bool|array|object|null $value
     * @return $this
     */
    public function set(string $key, string|int|float|bool|null|array|object $value): self
    {
        $this->setPayload($key, $value);
        return $this;
    }

    /**
     * Flushes entire Payload object
     * @return void
     */
    public function flush(): void
    {
        $this->data = [];
        $this->count = 0;
    }
}
