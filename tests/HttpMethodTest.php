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

use Charcoal\HTTP\Commons\HttpMethod;

/**
 * Class HttpMethodTest
 */
class HttpMethodTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function testHttpMethodEnum(): void
    {
        $this->assertEquals(HttpMethod::GET, HttpMethod::tryFrom("GET"));
        /** @noinspection PhpCaseWithValueNotFoundInEnumInspection */
        $this->assertNull(HttpMethod::tryFrom("get"), "Always pass uppercase HTTP methods");
        $this->assertNotEquals(HttpMethod::POST, HttpMethod::tryFrom("GET"));

        $this->assertEquals(HttpMethod::POST, HttpMethod::tryFrom("POST"));
        $this->assertEquals(HttpMethod::PUT, HttpMethod::tryFrom("PUT"));
        $this->assertEquals(HttpMethod::DELETE, HttpMethod::tryFrom("DELETE"));
        $this->assertEquals(HttpMethod::OPTIONS, HttpMethod::tryFrom("OPTIONS"));
    }
}
