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
 * Class KeyValuePair
 * @package Charcoal\Http\Commons
 */
class KeyValuePair
{
    /**
     * @param string $key
     * @param string|int|float|bool|array|null $value
     */
    public function __construct(
        public readonly string                           $key,
        public readonly string|int|float|bool|null|array $value
    )
    {
    }
}