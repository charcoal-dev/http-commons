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

use Charcoal\Http\Commons\WritablePayload;

/**
 * Class PayloadTest
 */
class PayloadTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function testPayload(): void
    {
        $payload = new \Charcoal\Http\Commons\ReadOnlyPayload([
            "dump" => ["a", "b", 1, 2, chr(0) . "bad-strin" . chr(103), ["sub" => ["c", "h", "a", "\vr", "c\r\n", "o", "a\t", "l"]]],
            "test1" => "\0test",
            "test2" => "simple-str",
            "int" => 0xffff + 1,
            "int2" => "1234",
            "test3" => " this is a\tmulti-line\r\nmessage\r\nwith\r\nsome invalid chars" . chr(145),
        ]);

        $this->assertEquals("\0test", $payload->getUnsafe("test1"));
        $this->assertEquals("test", $payload->getASCII("test1"));
        $this->assertEquals(" this is a\tmulti-line\r\nmessage\r\nwith\r\nsome invalid chars" . chr(145), $payload->getUnsafe("test3"));
        $this->assertEquals("this is amulti-linemessagewithsome invalid chars", $payload->getASCII("test3"));
        $this->assertEquals("this is a\tmulti-line\nmessage\nwith\nsome invalid chars", $payload->getASCII("test3", "\t\n"));
        $this->assertEquals("this is amulti-linemessagewithsome invalid chars", $payload->getSanitized("test3"));

        $this->assertEquals(
            '["a","b",1,2,"\u0000bad-string",{"sub":["c","h","a","\u000br","c\r\n","o","a\t","l"]}]',
            json_encode($payload->getUnsafe("dump"))
        );

        $this->assertEquals(
            '["a","b",1,2,"bad-string",{"sub":["c","h","a","r","c","o","a","l"]}]',
            json_encode($payload->getSanitized("dump"))
        );

        $this->assertEquals(65536, $payload->getInt("int"));
        $this->assertEquals(1234, $payload->getInt("int2"));
        $this->assertNull($payload->getInt("test1"));
        $this->assertNull($payload->getInt("test3"));
    }

    public function testGetUnrecognizedKeysWithEmptyData(): void
    {
        // Empty payload
        $payload = new WritablePayload([], false);

        // Any allowed keys (even none) should return empty array
        $unrecognized = $payload->getUnrecognizedKeys('alpha', 'beta');
        $this->assertEmpty($unrecognized, 'Empty payload should not have unrecognized keys');
    }

    public function testGetUnrecognizedKeysNoAllowedKeys(): void
    {
        // Payload has some keys
        $payload = new WritablePayload([], false);
        $payload->set('k1', 'value1');
        $payload->set('k2', 'value2');

        // No allowed keys given, so all payload keys should be unrecognized
        $unrecognized = $payload->getUnrecognizedKeys();
        $this->assertCount(2, $unrecognized, 'Expected all payload keys to be unrecognized');
        $this->assertContains('k1', $unrecognized);
        $this->assertContains('k2', $unrecognized);
    }

    public function testGetUnrecognizedKeysPartialMatch(): void
    {
        $payload = new WritablePayload([], false);
        $payload->set('key1', 'value1');
        $payload->set('key2', 'value2');
        $payload->set('key3', 'value3');

        // Only key1 and key2 are recognized
        $unrecognized = $payload->getUnrecognizedKeys('key1', 'key2');
        $this->assertCount(1, $unrecognized);
        $this->assertSame(['key3'], $unrecognized);
    }

    public function testGetUnrecognizedKeysCaseInsensitivity(): void
    {
        $payload = new WritablePayload([], false);
        $payload->set('CaseKey', 'someValue');

        // Allowed keys differ by case
        $unrecognized = $payload->getUnrecognizedKeys('casekey');
        $this->assertEmpty(
            $unrecognized,
            'Keys should be recognized regardless of case, expecting no unrecognized keys'
        );
    }

    public function testIsRestrictedToKeysEmptyData(): void
    {
        // Empty payload again
        $payload = new WritablePayload([], false);

        // Even if many keys are allowed, empty data has no unrecognized keys
        $this->assertTrue($payload->isRestrictedToKeys('keyA', 'keyB'), 'Empty payload is trivially restricted');
    }

    public function testIsRestrictedToKeysAllAllowed(): void
    {
        $payload = new WritablePayload([], false);
        $payload->set('user', 'anyUser');
        $payload->set('email', 'anyEmail');

        // If both keys exist in allowed list, the payload is restricted
        $this->assertTrue($payload->isRestrictedToKeys('user', 'email'), 'All keys in payload should be allowed');
    }

    public function testIsRestrictedToKeysWithUnrecognizedKeys(): void
    {
        $payload = new WritablePayload([], false);
        $payload->set('allowedKey', 'someValue');
        $payload->set('forbiddenKey', 'someValue');

        // Only "allowedKey" is recognized
        $this->assertFalse(
            $payload->isRestrictedToKeys('allowedKey'),
            'Payload should not be restricted because "forbiddenKey" is unrecognized'
        );
    }
}