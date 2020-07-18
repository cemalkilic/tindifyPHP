<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyAPIWrapper {

    const DEFAULT_LIMIT  = 20;
    const DEFAULT_OFFSET = 0;

    private $api;
    private $apiRequestFilter;
    private $apiResponseFilter;
    private $em;
    private $username;

    public function __construct(SpotifyWebAPI $spotifyWebAPI,
                                SpotifyAPIRequestFilter $spotifyAPIRequestFilter,
                                SpotifyAPIResponseFilter $spotifyAPIResponseFilter,
                                EntityManagerInterface $entityManager
    ) {
        $this->api = $spotifyWebAPI;
        $this->apiRequestFilter = $spotifyAPIRequestFilter;
        $this->apiResponseFilter = $spotifyAPIResponseFilter;
        $this->em = $entityManager;
        $this->setDefaultAPIOptions();
    }

    public function setAccessToken($accessToken) {
        $this->api->setAccessToken($accessToken);
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPlaylistTracks($playlistID, $options = []) {

        $defaultOptions = $this->getDefaultRequestOptions();

        // Filter for needed fields
        $defaultOptions['fields'] = $this->apiRequestFilter->getTrackFilters();

        $options = $this->mergeOptionsArray($defaultOptions, $options);

        $songs = $this->api->getPlaylistTracks($playlistID, $options);

        $songs['items'] = array_map(function ($item) {
            $item['track'] = $this->apiResponseFilter->getSongFields($item["track"]);
            return $item;
        }, $songs['items']);

        return $songs;
    }

    public function getMyPlaylists($options = []) {

        $defaultOptions = $this->getDefaultRequestOptions();

        $options = $this->mergeOptionsArray($defaultOptions, $options);

        // TODO: Support for "Liked songs"

        $playlists = $this->api->getMyPlaylists($options);

        // Placeholder image for empty album covers
        $playlists["items"] = array_map(function ($item) {
            if (empty($item["images"])) {
                $item["images"][0]["url"] = "https://via.placeholder.com/300";
            }
            return $item;
        }, $playlists["items"]);

        return $playlists;
    }

    public function createPlaylist($options = []) {
        return $this->api->createPlaylist($options);
    }

    public function addPlaylistTracks($playlistID, $songIDs) {
        return $this->api->addPlaylistTracks($playlistID, $songIDs);
    }

    public function getTrack($trackID) {
        $song = $this->api->getTrack($trackID);

        $song = $this->apiResponseFilter->getSongFields($song);

        return $song;
    }

    public function getRecommendations($options) {
        $defaultOptions = $this->getDefaultRequestOptions();

        // Filtering fields for recommended song is not supported by Spotify
        // at the day of implementation
        // $options['fields'] = $this->apiRequestFilter->getTrackFilters();

        $options = $this->mergeOptionsArray($defaultOptions, $options);

        $songs = $this->api->getRecommendations($options);

        $songs['tracks'] = array_map(function ($item) {
            return $this->apiResponseFilter->getSongFields($item);
        }, $songs['tracks']);

        return $songs;
    }

    public function getSavedSongs($options = []) {
        $defaultOptions = $this->getDefaultRequestOptions();

        // Filter for needed fields
        $defaultOptions['fields'] = $this->apiRequestFilter->getTrackFilters();

        $options = $this->mergeOptionsArray($defaultOptions, $options);

        $songs = $this->api->getMySavedTracks($options);
        $songs['items'] = array_map(function ($item) {
            $item = $this->apiResponseFilter->getSongFields($item["track"]);
            return $item;
        }, $songs['items']);

        return $songs;
    }

    public function createTindifyPlaylist($options = []) {
        $defaultOptions = [
            "name" => "Tindify",
            "public" => false,
            "description" => "The songs you matched!"
        ];

        $options = $this->mergeOptionsArray($defaultOptions, $options);

        return $this->api->createPlaylist($options);
    }

    public function getPlaylistMeta($playlistID) {
        $options["fields"] = $this->apiRequestFilter->getPlaylistMetaFilters();

        return $this->api->getPlaylist($playlistID, $options);
    }

    public function getTindifySongs($options = []) {
        $tindifyPlaylistManager = new TindifyPlaylistManager($this->em, $this);
        $tindifyPlaylist = $tindifyPlaylistManager->getTindifyPlaylist();

        $defaultOptions = $this->getDefaultRequestOptions();

        // Filter for needed fields
        $defaultOptions['fields'] = $this->apiRequestFilter->getTrackFilters();
        $options = $this->mergeOptionsArray($defaultOptions, $options);

        $songs = $this->api->getPlaylistTracks($tindifyPlaylist->getPlaylistID(), $options);

        $songs['items'] = array_map(function ($item) {
            $item['track'] = $this->apiResponseFilter->getSongFields($item["track"]);
            return $item;
        }, $songs['items']);

        return $songs;
    }

    public function addTindifyPlaylistSongs($songIDs) {
        $tindifyPlaylistManager = new TindifyPlaylistManager($this->em, $this);

        $tindifyPlaylist = $tindifyPlaylistManager->getTindifyPlaylist();

        return $this->addPlaylistTracks($tindifyPlaylist->getPlaylistID(), $songIDs);
    }

    public function getPlaylistRecommendations($playlistID, $options = []) {
        $seedSongsLimit = 5; // limit by Spotify API

        $defaultOptions = $this->getDefaultRequestOptions();
        $options = $this->mergeOptionsArray($defaultOptions, $options);

        $playlistSongs = $this->getPlaylistTracks($playlistID);

        // Playlist might have no songs
        if (isset($playlistSongs["items"]) && empty($playlistSongs["items"])) {
            return $playlistSongs;
        }

        $randomIndexes = [];

        // Check playlist has songs more than limit
        $playlistSongCount = count($playlistSongs["items"]);
        if ($playlistSongCount > $seedSongsLimit) {
            while (count($randomIndexes) !== $seedSongsLimit) {
                $rand = random_int(0, count($playlistSongs["items"]) - 1);
                if (!in_array($rand, $randomIndexes)) {
                    $randomIndexes[] = $rand;
                }
            }
        } else {
            // select all indexes as random indexes
            $randomIndexes = array_keys($playlistSongs["items"]);
        }


        // get the IDs for random selected index
        $seedSongs = [];
        foreach ($randomIndexes as $randomNumber) {
            $seedSongs[] = $playlistSongs["items"][$randomNumber]["track"]["id"];
        }

        $options = $this->mergeOptionsArray($options, [
            "seed_tracks" => $seedSongs
        ]);

        $recommendations = $this->getRecommendations($options);

        // stay consistent with the array keys
        $recommendations["items"] = $recommendations["tracks"];
        unset($recommendations["tracks"]);

        return $recommendations;
    }

    private function setDefaultAPIOptions($options = []) {
        $defaultOptions = [
            "return_assoc" => true
        ];
        $options = array_merge($defaultOptions, $options);
        $this->api->setOptions($options);
    }

    private function getDefaultRequestOptions() {
        return [
            "limit"  => self::DEFAULT_LIMIT,
            "offset" => self::DEFAULT_OFFSET
        ];
    }

    private function mergeOptionsArray($defaultOptions, $options) {
        $options = array_filter($options, function($value, $key) {

            if ($key == "limit") {
                return is_numeric($value) && intval($value) >= 0;
            }

            if ($key == "offset") {
                return is_numeric($value) && intval($value) >= 0;
            }

            return isset($value);

        }, ARRAY_FILTER_USE_BOTH);

        return array_merge($defaultOptions, $options);
    }

}
