<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Tests\Commons;

use Charcoal\Http\Commons\Support\HttpHelper;

/**
 * Unit test class for testing the HttpHelper class, specifically the behavior of the `normalizeHostnamePort` method.
 * Verifies both valid and invalid inputs to ensure proper functioning according to the expected input/output criteria.
 */
class HttpHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function testMustAccept(): void
    {
        $this->assertSame(["www.example.com", null, false], HttpHelper::normalizeHostnamePort("www.Example.COM"), "FQDN");
        $this->assertSame(["example.com", 443, false], HttpHelper::normalizeHostnamePort("example.com:443"), "FQDN + port");
        $this->assertSame(["example.com", 65535, false], HttpHelper::normalizeHostnamePort("example.com:65535"), "Max port");
        $this->assertSame(["example.com", null, false], HttpHelper::normalizeHostnamePort("EXAMPLE.COM."), "Trailing dot");
        $this->assertSame(["example.com", null, false], HttpHelper::normalizeHostnamePort("   example.com   "), "Whitespace trimmed");
        $this->assertSame(["203.0.113.7", null, false], HttpHelper::normalizeHostnamePort("203.0.113.7"), "IPv4");
        $this->assertSame(["203.0.113.7", 1234, false], HttpHelper::normalizeHostnamePort("203.0.113.7:1234"), "IPv4 + port");
        $this->assertSame(["2001:db8::1", null, true], HttpHelper::normalizeHostnamePort("[2001:db8::1]"), "Bracketed IPv6");
        $this->assertSame(["2001:db8::1", 8080, true], HttpHelper::normalizeHostnamePort("[2001:db8::1]:8080"), "Bracketed IPv6 + port");
        $this->assertSame(["localhost", null, false], HttpHelper::normalizeHostnamePort("Localhost"), "Single-label host");
        $this->assertSame(["xn--d1acufc.xn--p1ai", null, false], HttpHelper::normalizeHostnamePort("xn--d1acufc.xn--p1ai"), "Punycode TLD");
        $this->assertSame(["example.com", 443, false], HttpHelper::normalizeHostnamePort("example.com.:443"), "Trailing dot + port");
        $this->assertSame(["2001:db8::a", null, true], HttpHelper::normalizeHostnamePort("[2001:DB8::A]"), "Bracketed IPv6 upper → lower");
        $this->assertSame(["2001:db8::1", 443, true], HttpHelper::normalizeHostnamePort(" [2001:db8::1]:443 "), "Trim + bracketed IPv6 + port");
        $this->assertSame(["example.com", 80, false], HttpHelper::normalizeHostnamePort("EXAMPLE.com:080"), "Leading zeros in port");
        $this->assertSame(["www.example.com", null, false], HttpHelper::normalizeHostnamePort("www.example.com."), "www + trailing dot");
        $this->assertSame(["example.com", 1, false], HttpHelper::normalizeHostnamePort("example.com:01"), "Leading zero → numeric port");
    }

    /**
     * @return void
     */
    public function testMustReject(): void
    {
        $this->assertFalse(HttpHelper::normalizeHostnamePort("2001:db8::1"), "Unbracketed IPv6");
        $this->assertFalse(HttpHelper::normalizeHostnamePort("example.com:"), "Empty port");
        $this->assertFalse(HttpHelper::normalizeHostnamePort("example.com:abc"), "Non-numeric port");
        $this->assertFalse(HttpHelper::normalizeHostnamePort("foo:bar:443"), "Extra colon junk");
        $this->assertFalse(HttpHelper::normalizeHostnamePort("[2001:db8::1"), "Missing closing bracket");
        $this->assertFalse(HttpHelper::normalizeHostnamePort("example..com"), "Double dot label");
        $this->assertFalse(HttpHelper::normalizeHostnamePort("-bad.com"), "Leading hyphen label");
        $this->assertFalse(HttpHelper::normalizeHostnamePort("*.example.com"), "Wildcard not allowed");
        $this->assertFalse(HttpHelper::normalizeHostnamePort("exa mple.com"), "Space in host");
        $this->assertFalse(HttpHelper::normalizeHostnamePort("[fe80::1%25eth0]"), "IPv6 zone id not allowed");
        $this->assertFalse(HttpHelper::normalizeHostnamePort("[2001:db8::1]443"), "Missing colon after bracket");
        $this->assertFalse(HttpHelper::normalizeHostnamePort("[]:443"), "Empty bracketed host");
        $this->assertFalse(HttpHelper::normalizeHostnamePort(":"), "Colon only");
        $this->assertFalse(HttpHelper::normalizeHostnamePort(""), "Empty string");
        $this->assertFalse(HttpHelper::normalizeHostnamePort("exámple.com"), "Non-ASCII label");
        $this->assertFalse(HttpHelper::normalizeHostnamePort("bad-.com"), "Trailing hyphen in label");
        $this->assertFalse(HttpHelper::normalizeHostnamePort("exa_mple.com"), "Underscore not allowed");
        $this->assertFalse(HttpHelper::normalizeHostnamePort("example .com"), "Internal space in host");
        $this->assertFalse(HttpHelper::normalizeHostnamePort("[FE80::1%25ETH0]"), "IPv6 zone id not allowed");
        $this->assertFalse(HttpHelper::normalizeHostnamePort("example.com:+80"), "Plus sign in port");
        $this->assertFalse(HttpHelper::normalizeHostnamePort("example.com:-1"), "Negative port");
        $this->assertFalse(HttpHelper::normalizeHostnamePort("[2001:db8::1] :443"), "Space between ] and :");
        $this->assertFalse(HttpHelper::normalizeHostnamePort("[2001:db8::1]junk"), "Garbage after bracket");
    }

    /**
     * @return void
     */
    public function testHostnamePassPortRejects():void
    {
        $this->assertSame(["2001:db8::1", null, true], HttpHelper::normalizeHostnamePort("[2001:db8::1]:"), "Empty port after bracket");
        $this->assertSame(["localhost", null, false], HttpHelper::normalizeHostnamePort("localhost:70000"), "Port too high");
        $this->assertSame(["example.com", null, false], HttpHelper::normalizeHostnamePort("example.com:70000"), "Port out of range");
        $this->assertSame(["example.com", null, false], HttpHelper::normalizeHostnamePort("example.com:0"), "Port zero → treated as absent");
        $this->assertSame(["2001:db8::1", null, true], HttpHelper::normalizeHostnamePort("[2001:db8::1]:abc"), "IPv6 with non-numeric port");
    }
}