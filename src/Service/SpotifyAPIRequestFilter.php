<?php


namespace App\Service;


class SpotifyAPIRequestFilter {

    public function getTrackFilters() {
        return [
            "limit,offset,total", // for succeeding requests
            "items.track(id,is_local,name,popularity,preview_url,uri)", // song related
            "items.track.album(id,images,name,uri)", // album related
            "items.track.artists(id,name,uri)" // artist related
        ];
    }

    public function getPlaylistMetaFilters() {
        return [
            "id,name,owner(id),description"
        ];
    }

}
