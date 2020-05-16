<?php


namespace App\Service;


use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyAPIWrapper {

    const DEFAULT_LIMIT  = 20;
    const DEFAULT_OFFSET = 0;

    private $api;
    private $apiRequestFilter;
    private $apiResponseFilter;

    public function __construct(SpotifyWebAPI $spotifyWebAPI,
                                SpotifyAPIRequestFilter $spotifyAPIRequestFilter,
                                SpotifyAPIResponseFilter $spotifyAPIResponseFilter
    ) {
        $this->api = $spotifyWebAPI;
        $this->apiRequestFilter = $spotifyAPIRequestFilter;
        $this->apiResponseFilter = $spotifyAPIResponseFilter;
        $this->setDefaultAPIOptions();
    }

    public function setAccessToken($accessToken) {
        $this->api->setAccessToken($accessToken);
    }

    public function getPlaylistTracks($playlistID, $options) {

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

        // will plug the liked songs as a playlist option
        // if only offset = 0 (on initial request)
        if ($defaultOptions["offset"] === 0) {
            $defaultOptions["limit"]--;
        }

        $options = $this->mergeOptionsArray($defaultOptions, $options);

        $playlists = $this->api->getMyPlaylists($options);

        if ($defaultOptions["offset"] === 0) {
            // TODO set album cover
            // for now copying an elem and overriding some values
            $likedSongPlaylist = $playlists["items"][2];
            $likedSongPlaylist["name"] = "Liked Songs";
            $likedSongPlaylist["id"] = "likedSongs";
            $likedSongPlaylist["tracks"]["total"] = 42;

            array_unshift($playlists["items"], $likedSongPlaylist);
        }

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
