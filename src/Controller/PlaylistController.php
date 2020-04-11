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

        $playlistName = $request->get("name") ?? "Tindify";
        $isPublic     = $request->get("public") ?? false;

        $playlistOptions = [
            "name"        => $playlistName,
            "public"      => $isPublic,
            "description" => "The songs you matched!"
        ];

        $res = [
            "success" => true,
            "message" => "Playlist created: " . $playlistName,
            "content" => []
        ];

        try {
            $res['content'] = $this->api->createPlaylist($playlistOptions);
        } catch (SpotifyWebAPIException $e) {
            $res = [
                "success" => false,
                "message" => $e->getReason() ?? $e->getMessage()
            ];
        }

        return JsonResponse::create($res);
    }
}
