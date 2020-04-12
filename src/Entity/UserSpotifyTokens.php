<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserSpotifyTokensRepository")
 */
class UserSpotifyTokens {

    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=255)
     */
    private $spotifyUserID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $accessToken;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $refreshToken;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $tokenExpire;

    public function getSpotifyUserID(): ?string {
        return $this->spotifyUserID;
    }

    public function setSpotifyUserID(?string $spotifyUserID): self {
        $this->spotifyUserID = $spotifyUserID;

        return $this;
    }

    public function getAccessToken(): ?string {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): self {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getRefreshToken(): ?string {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): self {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTokenExpire(): ?\DateTimeInterface {
        return $this->tokenExpire;
    }

    public function setTokenExpire(\DateTimeInterface $tokenExpire): self {
        $this->tokenExpire = $tokenExpire;

        return $this;
    }
}
