<?php


namespace App\Model;


use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface {

    private $username;
    private $accessToken;
    private $displayName;

    /**
     * User constructor.
     */
    public function __construct(String $username, String $accessToken) {
        $this->username = $username;
        $this->accessToken = $accessToken;
    }

    public function __toString() {
        return $this->getUsername();
    }

    public function getRoles() {
        return [];
    }

    public function getPassword() {
        // TODO: Implement getPassword() method.
    }

    public function getSalt() {
        // TODO: Implement getSalt() method.
    }

    public function getUsername() {
        return strval($this->username);
    }

    public function eraseCredentials() {
        // TODO: Implement eraseCredentials() method.
    }

    public function getAccessToken(): String {
        return $this->accessToken;
    }

    public function setAccessToken(String $accessToken): void {
        $this->accessToken = $accessToken;
    }

    public function getDisplayName() {
        return $this->displayName;
    }

    public function setDisplayName($displayName): void {
        $this->displayName = $displayName;
    }

}
