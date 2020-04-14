<?php


namespace App\Service;

use App\Entity\UserSpotifyTokens;
use Doctrine\ORM\EntityManagerInterface;

class SpotifyTokenPersister {

    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function persistSpotifyTokens(UserSpotifyTokens $spotifyTokens) {
        // Check if there is any record with the given id
        // If there is, update it
        // Otherwise, create a new one

        $userID = $spotifyTokens->getSpotifyUserID();
        $accessToken = $spotifyTokens->getAccessToken();
        $tokenExpire = $spotifyTokens->getTokenExpire();
        $refreshToken = $spotifyTokens->getRefreshToken();
        $createdAt = $spotifyTokens->getCreatedAt();

        // Check if there is any record for the user
        $existingSpotifyTokens = $this->em->getRepository(UserSpotifyTokens::class)->find($userID);
        if (!$existingSpotifyTokens) {
            // this is the first time user comes
            // store the tokens
            $this->em->persist($spotifyTokens);
        } else {
            // there is record for the user
            // lets update the access token, refresh token and expire time
            $existingSpotifyTokens->setAccessToken($accessToken);
            $existingSpotifyTokens->setRefreshToken($refreshToken);
            $existingSpotifyTokens->setTokenExpire($tokenExpire);
            $existingSpotifyTokens->setCreatedAt($createdAt);
        }

        $this->em->flush();
    }
}
