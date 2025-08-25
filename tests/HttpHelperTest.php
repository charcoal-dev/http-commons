<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Tests\Commons;

use Charcoal\Http\Commons\Support\HttpHelper;

/**
 * Unit test class for testing the HttpHelper class, specifically the behavior of the `normalizeHostname` method.
 * Verifies both valid and invalid inputs to ensure proper functioning according to the expected input/output criteria.
 */
class HttpHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function testMustAccept(): void
    {
        $this->assertSame("www.example.com", HttpHelper::normalizeHostname("www.Example.COM"), "FQDN");
        $this->assertSame(["example.com", 443], HttpHelper::normalizeHostname("example.com:443"), "FQDN + port");
        $this->assertSame(["example.com", 65535], HttpHelper::normalizeHostname("example.com:65535"), "Max port");
        $this->assertSame("example.com", HttpHelper::normalizeHostname("EXAMPLE.COM."), "Trailing dot");
        $this->assertSame("example.com", HttpHelper::normalizeHostname("   example.com   "), "Whitespace trimmed");
        $this->assertSame("203.0.113.7", HttpHelper::normalizeHostname("203.0.113.7"), "IPv4");
        $this->assertSame(["203.0.113.7", 1234], HttpHelper::normalizeHostname("203.0.113.7:1234"), "IPv4 + port");
        $this->assertSame("2001:db8::1", HttpHelper::normalizeHostname("[2001:db8::1]"), "Bracketed IPv6");
        $this->assertSame(["2001:db8::1", 8080], HttpHelper::normalizeHostname("[2001:db8::1]:8080"), "Bracketed IPv6 + port");
        $this->assertSame("localhost", HttpHelper::normalizeHostname("Localhost"), "Single-label host");
        $this->assertSame("xn--d1acufc.xn--p1ai", HttpHelper::normalizeHostname("xn--d1acufc.xn--p1ai"), "Punycode TLD");
        $this->assertSame(["example.com", 443], HttpHelper::normalizeHostname("example.com.:443"), "Trailing dot + port");
        $this->assertSame("2001:db8::a", HttpHelper::normalizeHostname("[2001:DB8::A]"), "Bracketed IPv6 upper → lower");
        $this->assertSame(["2001:db8::1", 443], HttpHelper::normalizeHostname(" [2001:db8::1]:443 "), "Trim + bracketed IPv6 + port");
        $this->assertSame(["example.com", 80], HttpHelper::normalizeHostname("EXAMPLE.com:080"), "Leading zeros in port");
        $this->assertSame("www.example.com", HttpHelper::normalizeHostname("www.example.com."), "www + trailing dot");
        $this->assertSame(["example.com", 1], HttpHelper::normalizeHostname("example.com:01"), "Leading zero → numeric port");
    }

    /**
     * @return void
     */
    public function testMustReject(): void
    {
        $this->assertFalse(HttpHelper::normalizeHostname("2001:db8::1"), "Unbracketed IPv6");
        $this->assertFalse(HttpHelper::normalizeHostname("example.com:"), "Empty port");
        $this->assertFalse(HttpHelper::normalizeHostname("example.com:abc"), "Non-numeric port");
        $this->assertFalse(HttpHelper::normalizeHostname("foo:bar:443"), "Extra colon junk");
        $this->assertFalse(HttpHelper::normalizeHostname("[2001:db8::1"), "Missing closing bracket");
        $this->assertFalse(HttpHelper::normalizeHostname("example..com"), "Double dot label");
        $this->assertFalse(HttpHelper::normalizeHostname("-bad.com"), "Leading hyphen label");
        $this->assertFalse(HttpHelper::normalizeHostname("*.example.com"), "Wildcard not allowed");
        $this->assertFalse(HttpHelper::normalizeHostname("exa mple.com"), "Space in host");
        $this->assertFalse(HttpHelper::normalizeHostname("[fe80::1%25eth0]"), "IPv6 zone id not allowed");
        $this->assertFalse(HttpHelper::normalizeHostname("[2001:db8::1]443"), "Missing colon after bracket");
        $this->assertFalse(HttpHelper::normalizeHostname("[]:443"), "Empty bracketed host");
        $this->assertFalse(HttpHelper::normalizeHostname(":"), "Colon only");
        $this->assertFalse(HttpHelper::normalizeHostname(""), "Empty string");
        $this->assertFalse(HttpHelper::normalizeHostname("exámple.com"), "Non-ASCII label");
        $this->assertFalse(HttpHelper::normalizeHostname("bad-.com"), "Trailing hyphen in label");
        $this->assertFalse(HttpHelper::normalizeHostname("exa_mple.com"), "Underscore not allowed");
        $this->assertFalse(HttpHelper::normalizeHostname("example .com"), "Internal space in host");
        $this->assertFalse(HttpHelper::normalizeHostname("[FE80::1%25ETH0]"), "IPv6 zone id not allowed");
        $this->assertFalse(HttpHelper::normalizeHostname("example.com:+80"), "Plus sign in port");
        $this->assertFalse(HttpHelper::normalizeHostname("example.com:-1"), "Negative port");
        $this->assertFalse(HttpHelper::normalizeHostname("[2001:db8::1] :443"), "Space between ] and :");
        $this->assertFalse(HttpHelper::normalizeHostname("[2001:db8::1]junk"), "Garbage after bracket");
    }

    /**
     * @return void
     */
    public function testHostnamePassPortRejects():void
    {
        $this->assertSame(["2001:db8::1", null], HttpHelper::normalizeHostname("[2001:db8::1]:"), "Empty port after bracket");
        $this->assertSame(["localhost", null], HttpHelper::normalizeHostname("localhost:70000"), "Port too high");
        $this->assertSame(["example.com", null], HttpHelper::normalizeHostname("example.com:70000"), "Port out of range");
        $this->assertSame(["example.com", null], HttpHelper::normalizeHostname("example.com:0"), "Port zero → treated as absent");
        $this->assertSame(["2001:db8::1", null], HttpHelper::normalizeHostname("[2001:db8::1]:abc"), "IPv6 with non-numeric port");
    }
}