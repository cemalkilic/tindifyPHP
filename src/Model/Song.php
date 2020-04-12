<?php

namespace App\Model;

class Song {
    private $id;
    private $songName;
    private $previewURL;
    private $artistName;
    private $albumName;
    private $albumImage;
    private $isLocal;

    public function getId() {
        return $this->id;
    }

    public function setId($id): void {
        $this->id = $id;
    }

    public function getSongName() {
        return $this->songName;
    }

    public function setSongName($songName): void {
        $this->songName = $songName;
    }

    public function getPreviewURL() {
        return $this->previewURL;
    }

    public function setPreviewURL($previewURL): void {
        $this->previewURL = $previewURL;
    }

    public function getArtistName() {
        return $this->artistName;
    }

    public function setArtistName($artistName): void {
        $this->artistName = $artistName;
    }

    public function getAlbumName() {
        return $this->albumName;
    }

    public function setAlbumName($albumName): void {
        $this->albumName = $albumName;
    }

    public function getAlbumImage() {
        return $this->albumImage;
    }

    public function setAlbumImage($albumImage): void {
        $this->albumImage = $albumImage;
    }

    public function getIsLocal() {
        return $this->isLocal;
    }

    public function setIsLocal($isLocal): void {
        $this->isLocal = $isLocal;
    }

    public function serializeToArray() {
        return get_object_vars($this);
    }

    // TODO must be a better way!
    public static function createFromTrackDetails($track) {
        $song = new Song();
        $song->setID($track->id);
        $song->setSongName($track->name);
        $song->setPreviewURL($track->preview_url ?? '');

        $artistName = implode(", ", array_map(function($artist) {
            return $artist->name;
        }, $track->artists));
        $song->setArtistName($artistName);

        $song->setAlbumName($track->album->name);
        $song->setAlbumImage($track->album->images[0]->url);

        $song->setIsLocal($track->is_local);

        return $song;
    }

}
