<?php


namespace App\Controller;


use App\Model\Song;
use SpotifyWebAPI\SpotifyWebAPIException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SongController extends BaseController {

    public function getSong(Request $request) {

        $songID = $request->attributes->get('id', null);

        try {
            $song = $this->api->getTrack($songID);
            $song = new Song($song);
            return JsonResponse::create($song->serializeToArray());
        } catch (SpotifyWebAPIException $e) {
            $errorDetails = $e->getReason() ?? $e->getMessage();
            return JsonResponse::create($errorDetails, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getRecommendations(Request $request) {
        $seedSongs = $request->request->get('seedSongs', null);
        if (empty($seedSongs)) {
            $errorDetails = "No seed songs givens!";
            return JsonResponse::create($errorDetails, JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!is_array($seedSongs)) {
            $errorDetails = "Seed songs should be given as an array of songs IDs!";
            return JsonResponse::create($errorDetails, JsonResponse::HTTP_BAD_REQUEST);
        }

        $options = [
            "limit"       => 20, // number of returned songs
            "seed_tracks" => $seedSongs
        ];

        try {
            $songs = $this->api->getRecommendations($options);

            $songs['tracks'] = array_map(function($item) {
                $song = new Song($item);
                return $song->serializeToArray();
            }, $songs['tracks']);

            return JsonResponse::create($songs);
        } catch (SpotifyWebAPIException $e) {
            $errorDetails = $e->getReason() ?? $e->getMessage();
            return JsonResponse::create($errorDetails, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getSavedSongs() {
        try {
            $songs = $this->api->getSavedSongs();

            $songs['items'] = array_map(function($item) {
                $song = new Song($item);
                return $song->serializeToArray();
            }, $songs['items']);

            return JsonResponse::create($songs);
        } catch (SpotifyWebAPIException $e) {
            $errorDetails = $e->getReason() ?? $e->getMessage();
            return JsonResponse::create($errorDetails, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
