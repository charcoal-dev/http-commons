<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Tests\Commons;

use Charcoal\Base\Abstracts\Dataset\BatchEnvelope;
use Charcoal\Base\Abstracts\Dataset\KeyValue;
use Charcoal\Base\Enums\ExceptionAction;
use Charcoal\Http\Commons\Headers\Headers;

/**
 * Class HeadersTest
 */
class HeadersTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param array $seed
     * @return Headers
     * @throws \Charcoal\Base\Exceptions\WrappedException
     */
    protected static function getSeededHeaders(array $seed): Headers
    {
        return new Headers(new BatchEnvelope($seed, ExceptionAction::Ignore));
    }

    /**
     * @return void
     * @throws \Charcoal\Base\Exceptions\WrappedException
     */
    public function testReadOnlyHeaders(): void
    {
        $headers = static::getSeededHeaders([
            "Content-Type" => "application/json",
            "Accept" => "json",
            "X-Charcoal-App" => "MyTestApp"
        ]);

        $this->assertEquals(3, $headers->count());
        foreach ($headers as $header) {
            $this->assertInstanceOf(KeyValue::class, $header);
        }

        $this->assertEquals("MyTestApp", $headers->get("x-charcoal-app"));
        $this->assertEquals("application/json", $headers->get("content-type"));
    }

    /**
     * @return void
     * @throws \Charcoal\Base\Exceptions\WrappedException
     */
    public function testWritableHeaders(): void
    {
        $headers = new Headers(new BatchEnvelope([
            "Content-Type" => "application/json",
            "Accept" => "json",
            "X-Charcoal-App" => "MyTestApp"
        ], ExceptionAction::Ignore));

        $this->assertCount(3, $headers);
        $headers->set("Valid-Header", "Some valid value");
        $this->assertCount(4, $headers);
    }
}
