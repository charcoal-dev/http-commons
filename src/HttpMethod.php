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
 * Class HttpMethod
 * @package Charcoal\HTTP\Commons
 */
enum HttpMethod: string
{
    case GET = "get";
    case POST = "post";
    case PUT = "put";
    case DELETE = "delete";
    case OPTIONS = "options";
}
