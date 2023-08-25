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
 * Class PayloadTest
 */
class PayloadTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function testPayload(): void
    {
        $payload = new \Charcoal\HTTP\Commons\ReadOnlyPayload([
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
    }
}