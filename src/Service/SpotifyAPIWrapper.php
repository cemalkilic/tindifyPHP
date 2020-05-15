<?php


namespace App\Service;


use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyAPIWrapper {

    const DEFAULT_LIMIT  = 20;
    const DEFAULT_OFFSET = 0;

    private $api;
    private $apiRequestFilter;

    public function __construct(SpotifyWebAPI $spotifyWebAPI,
                                SpotifyAPIRequestFilter $spotifyAPIRequestFilter
    ) {
        $this->api = $spotifyWebAPI;
        $this->apiRequestFilter = $spotifyAPIRequestFilter;
        $this->setDefaultAPIOptions();
    }

    public function setAccessToken($accessToken) {
        $this->api->setAccessToken($accessToken);
    }

    public function getPlaylistTracks($playlistID, $options) {

        $defaultOptions = $this->getDefaultRequestOptions();

        // Filter for needed fields
        $defaultOptions['fields'] = $this->apiRequestFilter->getTrackFilters();

        $options = array_merge($defaultOptions, $options);

        return $this->api->getPlaylistTracks($playlistID, $options);
    }

    public function getMyPlaylists($options = []) {

        $defaultOptions = $this->getDefaultRequestOptions();

        $options = array_merge($defaultOptions, $options);

        return $this->api->getMyPlaylists($options);
    }

    public function createPlaylist($options = []) {
        return $this->api->createPlaylist($options);
    }

    public function addPlaylistTracks($playlistID, $songIDs) {
        return $this->api->addPlaylistTracks($playlistID, $songIDs);
    }

    public function getTrack($trackID) {
        return $this->api->getTrack($trackID);
    }

    public function getRecommendations($options) {
        $defaultOptions = $this->getDefaultRequestOptions();

        // Filtering fields for recommended song is not supported by Spotify
        // at the day of implementation
        // $options['fields'] = $this->apiRequestFilter->getTrackFilters();

        $options = array_merge($defaultOptions, $options);

        return $this->api->getRecommendations($options);
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

}
