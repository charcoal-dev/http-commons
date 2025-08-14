<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Tests\Commons;

use Charcoal\Base\Enums\Charset;
use Charcoal\Base\Enums\ExceptionAction;
use Charcoal\Base\Enums\ValidationState;
use Charcoal\Base\Support\Data\BatchEnvelope;
use Charcoal\Base\Support\Data\CheckedKeyValue;
use Charcoal\Http\Commons\Data\HttpDataPolicy;
use Charcoal\Http\Commons\Enums\HeaderKeyPolicy;
use Charcoal\Http\Commons\Exception\InvalidHeaderValueException;
use Charcoal\Http\Commons\Header\Headers;
use Charcoal\Http\Commons\Header\WritableHeaders;

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
    protected static function getSeededHeaders(array $seed)
    {
        return new Headers(
            static::getHeadersPolicy(),
            HeaderKeyPolicy::STRICT,
            new BatchEnvelope($seed, ExceptionAction::Ignore)
        );
    }

    /**
     * @return HttpDataPolicy
     */
    protected static function getHeadersPolicy()
    {
        return new HttpDataPolicy(
            Charset::ASCII,
            keyMaxLength: 64,
            keyOverflowTrim: false,
            valueMaxLength: 2048,
            valueOverflowTrim: false,
            accessKeyTrust: ValidationState::VALIDATED,
            setterKeyTrust: ValidationState::RAW,
            valueTrust: ValidationState::RAW,
        );
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
            $this->assertInstanceOf(CheckedKeyValue::class, $header);
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
        $headers = new WritableHeaders(
            static::getHeadersPolicy(),
            HeaderKeyPolicy::STRICT,
            new BatchEnvelope([
                "Content-Type" => "application/json",
                "Accept" => "json",
                "X-Charcoal-App" => "MyTestApp"
            ], ExceptionAction::Ignore));

        $this->assertCount(3, $headers);
        $headers->set("Valid-Header", "Some valid value");
        $this->assertCount(4, $headers);

        $this->expectException(InvalidHeaderValueException::class);
        $headers->set("X-Some-Value", chr(250) . "tes" . chr(116) . chr(128));
        $this->assertEquals("test", $headers->get("x-some-value"));
    }
}
