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

/**
 * Class HeadersTest
 */
class HeadersTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function testReadOnlyHeaders(): void
    {
        $headers = new \Charcoal\HTTP\Commons\Headers([
            "Content-Type" => "application/json",
            "Accept" => "json",
            "X-Charcoal-App" => "MyTestApp"
        ]);

        $this->assertEquals(3, $headers->count());
        foreach ($headers as $header) {
            $this->assertInstanceOf(\Charcoal\HTTP\Commons\KeyValuePair::class, $header);
        }

        $this->assertEquals("MyTestApp", $headers->get("x-charcoal-app"));
        $this->assertEquals("application/json", $headers->get("content-type"));
    }

    /**
     * @return void
     */
    public function testWritableHeaders(): void
    {
        $headers = new \Charcoal\HTTP\Commons\WritableHeaders([
            "Content-Type" => "application/json",
            "Accept" => "json",
            "X-Charcoal-App" => "MyTestApp"
        ]);

        $headers->sanitizeValues = true;
        $headers->set("X-Some-Value", chr(250) . "tes" . chr(116) . chr(128));
        $this->assertEquals("test", $headers->get("x-some-value"));
        $count = $headers->count();

        $headers->validateKeys = false;
        $headers->set("Inv@lid-Key", "test-value");
        $this->assertEquals("test-value", $headers->get("Inv@lid-Key"));
        $this->assertEquals($count + 1, $headers->count());
        $count = $headers->count();

        $headers->validateKeys = true;
        $headers->set("Inv@lid-Key-2", "test-value");
        // Verify that invalid key wasn't added:
        $this->assertEquals($count, $headers->count());
    }

    /**
     * @return void
     */
    public function testValidations(): void
    {
        $badHeaders = [
            "Content-Type" => "application/json",
            "Th&sd" => "bad-key-value",
            "X-Charcoal-App" => "MyTestApp",
            "X-Bad-Value" => "this has bad value " . chr(128)
        ];

        $headers1 = new \Charcoal\HTTP\Commons\Headers($badHeaders, validateKeys: false, sanitizeValues: false);
        $this->assertEquals(4, $headers1->count());
        $this->assertEquals($badHeaders["X-Bad-Value"], $headers1->get("x-bad-value"));
        $this->assertEquals("bad-key-value", $headers1->get("th&sd"));
        unset($headers1);

        $headers2 = new \Charcoal\HTTP\Commons\Headers($badHeaders, validateKeys: true, sanitizeValues: false);
        $this->assertEquals(3, $headers2->count());
        $this->assertEquals($badHeaders["X-Bad-Value"], $headers2->get("x-bad-value"));
        $this->assertNull($headers2->get("th&sd")); // This wasn't set
        unset($headers2);

        $headers3 = new \Charcoal\HTTP\Commons\Headers($badHeaders, validateKeys: true, sanitizeValues: true);
        $this->assertEquals(3, $headers3->count());
        // Last character was stripped by sanitizer:
        $this->assertEquals("this has bad value ", $headers3->get("x-bad-value"));
        $this->assertNull($headers3->get("th&sd")); // This wasn't set
    }
}
