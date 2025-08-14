<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Contracts;

use Charcoal\Http\Commons\Enums\Security\CredentialEncoding;
use Charcoal\Http\Commons\Enums\Security\CredentialType;

/**
 * Interface CredentialObjectInterface
 * @package Charcoal\Http\Commons\Contracts
 */
interface CredentialObjectInterface
{
    public function filepath(): string;

    public function type(): CredentialType;

    public function encoding(): CredentialEncoding;
}