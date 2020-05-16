<?php


namespace App\Service;


use App\Entity\TindifyPlaylist;
use Doctrine\ORM\EntityManagerInterface;
use SpotifyWebAPI\SpotifyWebAPIException;

class TindifyPlaylistManager {

    private $em;
    private $api;

    public function __construct(EntityManagerInterface $entityManager,
                                SpotifyAPIWrapper $spotifyAPIWrapper
    ) {
        $this->em = $entityManager;
        $this->api = $spotifyAPIWrapper;
    }

    public function getTindifyPlaylist() {
        $username = $this->api->getUsername();
        if (empty($username)) {
            throw new \InvalidArgumentException("Username should not be empty!");
        }

        $tindifyPlaylist = $this->em->getRepository(TindifyPlaylist::class)->findOneByUsername($username);

        if (!isset($tindifyPlaylist)) {
            // No tindify playlist exist for the user, create a new one
            $tindifyPlaylist = $this->createTindifyPlaylist();
        }

        try {
            // Make sure playlist still exists in Spotify
            $tindifyPlaylistID = $tindifyPlaylist->getPlaylistID();
            $existPlaylist = $this->api->getPlaylistMeta($tindifyPlaylistID);
        } catch (SpotifyWebAPIException $e) {

            // If any other exception thrown, pass it to caller
            if ($e->getMessage() !== "Not found.") {
                throw $e;
            }

            // means playlist not exist, create a new one and use it
            $tindifyPlaylist = $this->createTindifyPlaylist();
        }

        return $tindifyPlaylist;
    }

    public function removeAllByUsername() {
        $username = $this->api->getUsername();
        $existingTindifyPlaylists = $this->em->getRepository(TindifyPlaylist::class)->findAllByUsername($username);
        foreach ($existingTindifyPlaylists as $playlist) {
            $this->em->remove($playlist);
        }
    }

    public function createTindifyPlaylist($options = []) {
        // Remove existing tindify playlists in DB
        $this->removeAllByUsername();

        $apiResponse = $this->api->createTindifyPlaylist($options);

        $tindifyPlaylist = new TindifyPlaylist();
        $tindifyPlaylist->setPlaylistID($apiResponse["id"]);
        $tindifyPlaylist->setName($apiResponse["name"]);
        $tindifyPlaylist->setDescription($apiResponse["description"]);
        $tindifyPlaylist->setUsername($apiResponse["owner"]["id"]);

        $this->em->persist($tindifyPlaylist);
        $this->em->flush();

        return $tindifyPlaylist;
    }

}
