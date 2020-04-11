<?php


namespace App\Controller;


use SpotifyWebAPI\SpotifyWebAPI;
use SpotifyWebAPI\SpotifyWebAPIException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PlaylistController extends AbstractController {

    protected $container;
    private $api;

    public function __construct(ContainerInterface $container, SpotifyWebAPI $api) {
        $this->container = $container;
        $this->api = $api;
        $this->setupAPI();
    }

    public function setupAPI() {
        $accessToken = $this->getUser()->getAccessToken();
        $this->api->setAccessToken($accessToken);
    }

    public function getUserPlaylists() {
        $res = $this->api->getMyPlaylists(["limit" => 5]);
        return JsonResponse::create($res->items);
    }

    public function createTindifyPlaylist(Request $request) {

        $playlistName = $request->request->get("name") ?? "Tindify";
        $isPublic     = $request->request->get("public") ?? false;

        $playlistOptions = [
            "name"        => $playlistName,
            "public"      => $isPublic,
            "description" => "The songs you matched!"
        ];

        $res = [
            "success" => false,
            "message" => "Playlist created: " . $playlistName,
            "content" => []
        ];

        try {
            $res['content'] = $this->api->createPlaylist($playlistOptions);
            $res['success'] = true;
        } catch (SpotifyWebAPIException $e) {
            $res['message'] =$e->getReason() ?? $e->getMessage();
        }

        return JsonResponse::create($res);
    }

    public function addSongsToPlaylist(Request $request) {
        $res = [
            "success" => false,
            "message" => "Song(s) added to playlist!",
            "content" => []
        ];

        $playlistID = $request->attributes->get('id', null);
        $songIDs    = $request->request->get('songIDs', null);

        if (empty($songIDs)) {
            $res['message'] = "No song ID given!";
            return JsonResponse::create($res, 400);
        }

        try {
            $res['content'] = $this->api->addPlaylistTracks($playlistID, $songIDs);
            $res['success'] = true;
        } catch (SpotifyWebAPIException $e) {
            $res['message'] = $e->getReason() ?? $e->getMessage();
        }

        return JsonResponse::create($res);
    }
}
