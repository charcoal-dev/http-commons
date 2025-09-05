<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Security;

use Charcoal\Base\Objects\Traits\NoDumpTrait;
use Charcoal\Http\Commons\Contracts\CredentialObjectInterface;
use Charcoal\Http\Commons\Enums\Security\CredentialEncoding;
use Charcoal\Http\Commons\Enums\Security\CredentialType;
use Charcoal\Http\Commons\Exceptions\CredentialFileException;
use Charcoal\Http\Commons\Support\SecurityHelper;

/**
 * Class AbstractCredentialFile
 * @package Charcoal\Http\Commons\Security
 */
class CredentialFilepath implements CredentialObjectInterface
{
    protected const int MAX_LENGTH = 2048;

    public readonly CredentialType $type;
    private bool $validated = false;

    use NoDumpTrait;

    /**
     * @param string $filepath
     * @param CredentialEncoding $encoding
     * @param bool $revalidateOnUnserialize
     * @throws CredentialFileException
     */
    public function __construct(
        #[\SensitiveParameter]
        public readonly string             $filepath,
        public readonly CredentialEncoding $encoding,
        public bool                        $revalidateOnUnserialize = true,
    )
    {
        $this->validate();
    }

    /**
     * @return void
     * @throws CredentialFileException
     */
    final protected function validate(): void
    {
        $this->validateFileIsReadable();
        $this->validateCredential();
        $this->validated = true;
    }

    /**
     * @return string|null
     * @throws CredentialFileException
     */
    protected function validateCredential(): ?string
    {
        $contents = $this->readFromFile();
        $type = match ($this->encoding) {
            CredentialEncoding::PEM => $this->resolveTypeFromPEM($contents),
            CredentialEncoding::DER =>
            throw new CredentialFileException($this, "DER encoding support is not implemented", 170),
        };

        if (!isset($this->type)) {
            $this->type = $type;
        } else {
            if ($this->type !== $type) {
                throw new CredentialFileException($this, sprintf(
                    'Credential file "%s" type mismatch %s === %s',
                    basename($this->filepath),
                    $this->type->name,
                    $type->name,
                ), 141);
            }
        }

        return $contents;
    }

    /**
     * @param string $contents
     * @return CredentialType
     * @throws CredentialFileException
     */
    protected function resolveTypeFromPEM(string $contents): CredentialType
    {
        $type = SecurityHelper::checkPemCredential($contents);
        if (!$type) {
            throw new CredentialFileException($this, "Invalid PEM credential type", 160);
        }

        return $type;
    }

    /**
     * @return void
     * @throws CredentialFileException
     */
    private function validateFileIsReadable(): void
    {
        $basename = basename($this->filepath);
        if (!file_exists($this->filepath) || !is_file($this->filepath)) {
            throw new CredentialFileException($this,
                sprintf('Credential file "%s" not found', $basename), 101);
        } else if (!is_readable($this->filepath)) {
            throw new CredentialFileException($this,
                sprintf('Credential file "%s" is not readable', $basename), 102);
        }
    }

    /**
     * @return array
     */
    public function __serialize(): array
    {
        return [
            "filepath" => $this->filepath,
            "type" => $this->type,
            "encoding" => $this->encoding,
            "validated" => $this->validated,
            "revalidateOnUnserialize" => $this->revalidateOnUnserialize,
        ];
    }

    /**
     * @param array $data
     * @return void
     * @throws CredentialFileException
     */
    public function __unserialize(array $data): void
    {
        $this->filepath = $data["filepath"];
        $this->type = $data["type"];
        $this->encoding = $data["encoding"];
        $this->validated = $data["validated"];
        $this->revalidateOnUnserialize = $data["revalidateOnUnserialize"];
        if ($this->revalidateOnUnserialize) {
            $this->validate();
        }
    }

    /**
     * @return string
     * @throws CredentialFileException
     */
    protected function readFromFile(): string
    {
        $basename = basename($this->filepath);
        $read = file_get_contents($this->filepath, length: static::MAX_LENGTH);
        if (!$read) {
            throw new CredentialFileException($this,
                sprintf('Failed to read "%s" credential file', $basename), 111);
        }

        return trim($read);
    }

    /**
     * @return string
     */
    public function filepath(): string
    {
        return $this->filepath;
    }

    /**
     * @return CredentialType
     */
    public function type(): CredentialType
    {
        return $this->type;
    }

    /**
     * @return CredentialEncoding
     */
    public function encoding(): CredentialEncoding
    {
        return $this->encoding;
    }
}