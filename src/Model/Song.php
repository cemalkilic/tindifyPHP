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

    public function __construct($constructArray) {
        $this->id         = $constructArray["id"] ?? "";
        $this->songName   = $constructArray["songName"] ?? "";
        $this->previewURL = $constructArray["previewURL"] ?? "";
        $this->artistName = $constructArray["artistName"] ?? "";
        $this->albumName  = $constructArray["albumName"] ?? "";
        $this->albumImage = $constructArray["albumImage"] ?? "";
        $this->isLocal    = $constructArray["isLocal"];
    }

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

}
