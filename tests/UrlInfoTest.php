<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

/**
 * Class UrlInfoTest
 */
class UrlInfoTest extends \PHPUnit\Framework\TestCase
{
    /***
     * @return void
     */
    public function testUrlInfo(): void
    {
        $url1 = new \Charcoal\Http\Commons\Support\UrlInfo("https://charcoal.dev/docs/http/commons?a=1&b=false");
        $this->assertEquals("https", $url1->scheme);
        $this->assertEquals("charcoal.dev", $url1->host);
        $this->assertEquals("/docs/http/commons", $url1->path);
        $this->assertEquals("a=1&b=false", $url1->query);
        $this->assertNull($url1->fragment);
        $this->assertNull($url1->username);
        $this->assertNull($url1->password);
        unset($url1);

        $url2 = new \Charcoal\Http\Commons\Support\UrlInfo("/docs/http/commons?a=true&b=2#testFrag");
        $this->assertNull($url2->scheme);
        $this->assertNull($url2->host);
        $this->assertEquals("/docs/http/commons", $url2->path);
        $this->assertEquals("a=true&b=2", $url2->query);
        $this->assertEquals("testFrag", $url2->fragment);
        $this->assertNull($url2->username);
        $this->assertNull($url2->password);
        unset($url2);

        $url3 = new \Charcoal\Http\Commons\Support\UrlInfo("ftp://charcoal:secret@charcoal.dev");
        $this->assertEquals("ftp", $url3->scheme);
        $this->assertEquals("charcoal.dev", $url3->host);
        $this->assertEquals("charcoal", $url3->username);
        $this->assertEquals("secret", $url3->password);
    }
}
