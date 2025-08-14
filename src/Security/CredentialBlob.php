<?php
/**
 * Part of the "charcoal-dev/http-commons" package.
 * @link https://github.com/charcoal-dev/http-commons
 */

declare(strict_types=1);

namespace Charcoal\Http\Commons\Security;

use Charcoal\Base\Traits\NoDumpTrait;
use Charcoal\Http\Commons\Enums\Security\CredentialEncoding;
use Charcoal\Http\Commons\Exception\CredentialFileException;

/**
 * Class CredentialBlob
 * @package Charcoal\Http\Commons\Security
 */
class CredentialBlob extends CredentialFilepath
{
    private ?string $loaded = null;

    use NoDumpTrait;

    /**
     * @param string $filepath
     * @param CredentialEncoding $encoding
     * @param bool $revalidateOnUnserialize
     * @param bool $loadOnInitialize
     * @param bool $unloadOnSerialize
     * @throws CredentialFileException
     */
    public function __construct(
        #[\SensitiveParameter]
        string             $filepath,
        CredentialEncoding $encoding,
        bool               $revalidateOnUnserialize = true,
        public bool        $loadOnInitialize = true,
        public bool        $unloadOnSerialize = true,
    )
    {
        parent::__construct($filepath, $encoding, $revalidateOnUnserialize);
    }

    /**
     * @return string|null
     * @throws CredentialFileException
     */
    protected function validateCredential(): ?string
    {
        $contents = parent::validateCredential();
        if ($this->loadOnInitialize) {
            $this->loaded = $contents;
        }

        return null;
    }

    /**
     * @return array
     */
    public function __serialize(): array
    {
        $data = parent::__serialize();
        $data["loadOnInitialize"] = $this->loadOnInitialize;
        $data["unloadOnSerialize"] = $this->unloadOnSerialize;
        $data["loaded"] = $this->unloadOnSerialize ? null : $this->loaded;
        return $data;
    }

    /**
     * @param array $data
     * @return void
     * @throws CredentialFileException
     */
    public function __unserialize(array $data): void
    {
        $this->loaded = $data["loaded"];
        $this->loadOnInitialize = $data["loadOnInitialize"];
        $this->unloadOnSerialize = $data["unloadOnSerialize"];
        if ($data["revalidateOnUnserialize"]) {
            $this->loaded = null;
        }

        parent::__unserialize($data);
    }

    /**
     * @return string
     * @throws CredentialFileException
     */
    public function getBlob(): string
    {
        if ($this->loaded) {
            return $this->loaded;
        }

        $this->loaded = $this->readFromFile();
        return $this->loaded;
    }
}