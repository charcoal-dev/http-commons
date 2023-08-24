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

namespace Charcoal\HTTP\Commons;

/**
 * Class WritableHeaders
 * @package Charcoal\HTTP\Commons
 */
class WritableHeaders extends Headers
{
    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function set(string $key, string $value): static
    {
        $this->setHeader($key, $value);
        return $this;
    }

    /**
     * @return void
     */
    public function flush(): void
    {
        $this->data = [];
        $this->count = 0;
    }
}
