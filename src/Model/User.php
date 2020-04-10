<?php


namespace App\Model;


use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface {

    private $username;
    private $accessToken;


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

    /**
     * @return String
     */
    public function getAccessToken(): String {
         return $this->accessToken;
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

}
