<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

use Charcoal\Http\Commons\Enums\HttpMethod;

/**
 * Class HttpMethodTest
 */
class HttpMethodTest extends \PHPUnit\Framework\TestCase
{
    public function testHttpMethodFindCi(): void
    {
        $this->assertNull(HttpMethod::tryFrom("get"));
        $this->assertEquals(HttpMethod::GET, HttpMethod::find("get"));
    }
}
