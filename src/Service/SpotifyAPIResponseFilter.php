<?php


namespace App\Service;


class SpotifyAPIResponseFilter {

    public function getSongFields($songArray = []) {
        $id = $songArray["id"] ?? "";
        $songName = $songArray["name"] ?? "";
        $previewURL = $songArray["preview_url"] ?? "";

        $artistName = implode(", ", array_map(function($artist) {
            return $artist["name"] ?? "";
        }, $songArray["artists"]));

        $albumName = $songArray["album"]["name"] ?? "";
        $albumImage = $songArray["album"]["images"][0]["url"] ?? "";

        $isLocal = $songArray["is_local"];

        return [
            "id" => $id,
            "songName" => $songName,
            "previewURL" => $previewURL,
            "artistName" => $artistName,
            "albumName" => $albumName,
            "albumImage" => $albumImage,
            "isLocal" => $isLocal,
        ];
    }
}
