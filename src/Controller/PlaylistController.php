<?php


namespace App\Controller;


use App\Model\Song;
use SpotifyWebAPI\SpotifyWebAPIException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PlaylistController extends BaseController {

    public function getUserPlaylists(Request $request) {
        $limit  = $request->query->get("limit", null);
        $offset = $request->query->get("offset", null);
        try {
            $content = $this->api->getMyPlaylists(["limit" => $limit, "offset" => $offset]);
            return JsonResponse::create($content);
        } catch (SpotifyWebAPIException $e) {
            $errorDetails = $e->getReason() ?? $e->getMessage();
            return JsonResponse::create($errorDetails, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function createTindifyPlaylist(Request $request) {

        $playlistName = $request->request->get("name") ?? "Tindify";
        $isPublic     = $request->request->get("public") ?? false;

        $playlistOptions = [
            "name"        => $playlistName,
            "public"      => $isPublic,
            "description" => "The songs you matched!"
        ];

        try {
            $content = $this->api->createPlaylist($playlistOptions);
            return JsonResponse::create($content);
        } catch (SpotifyWebAPIException $e) {
            $errorDetails = $e->getReason() ?? $e->getMessage();
            return JsonResponse::create($errorDetails, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addSongsToPlaylist(Request $request) {

        $playlistID = $request->attributes->get('id', null);
        $songIDs    = $request->request->get('songIDs', null);

        if (empty($songIDs)) {
            $errorDetails = "No song ID given!";
            return JsonResponse::create($errorDetails, JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $content = $this->api->addPlaylistTracks($playlistID, $songIDs);
            return JsonResponse::create($content);
        } catch (SpotifyWebAPIException $e) {
            $errorDetails = $e->getReason() ?? $e->getMessage();
            return JsonResponse::create($errorDetails, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getPlaylistSongs(Request $request) {

        $playlistID = $request->attributes->get('id', null);
        $limit      = $request->query->get("limit", null);
        $offset     = $request->query->get("offset", null);

        $options = compact('limit', 'offset');

        try {
            $songs = $this->api->getPlaylistTracks($playlistID, $options);

            $songs["items"] = array_map(function($item) {
                $song = new Song($item["track"]);
                return $song->serializeToArray();
            }, $songs["items"]);

            return JsonResponse::create($songs);
        } catch (SpotifyWebAPIException $e) {
            $errorDetails = $e->getReason() ?? $e->getMessage();
            return JsonResponse::create($errorDetails, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
