<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User {

    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $spotifyUserID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $display_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uri;

    public function __construct($spotifyUserID) {
        $this->spotifyUserID = $spotifyUserID;
        $this->uri = "spotify:user:" . $this->spotifyUserID;
    }

    public function getSpotifyUserID(): ?string {
        return $this->spotifyUserID;
    }

    public function setSpotifyUserID(string $spotifyUserID): self {
        $this->spotifyUserID = $spotifyUserID;

        return $this;
    }

    public function getDisplayName(): ?string {
        return $this->display_name;
    }

    public function setDisplayName(?string $display_name): self {
        $this->display_name = $display_name;

        return $this;
    }

    public function getUri(): ?string {
        return $this->uri;
    }

    public function setUri(string $uri): self {
        $this->uri = $uri;

        return $this;
    }

}
