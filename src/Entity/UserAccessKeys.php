<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserAccessKeysRepository")
 */
class UserAccessKeys {

    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=32)
     */
    private $accessKey;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $spotifyUserID;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expireAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function getAccessKey(): ?string {
        return $this->accessKey;
    }

    public function setAccessKey(string $accessKey): self {
        $this->accessKey = $accessKey;

        return $this;
    }

    public function getSpotifyUserID(): ?string {
        return $this->spotifyUserID;
    }

    public function setSpotifyUserID(string $spotifyUserID): self {
        $this->spotifyUserID = $spotifyUserID;

        return $this;
    }

    public function getExpireAt(): ?\DateTimeInterface {
        return $this->expireAt;
    }

    public function setExpireAt(\DateTimeInterface $expireAt): self {
        $this->expireAt = $expireAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self {
        $this->createdAt = $createdAt;

        return $this;
    }
}
