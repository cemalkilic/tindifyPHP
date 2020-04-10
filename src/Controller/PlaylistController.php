<?php


namespace App\Controller;


use SpotifyWebAPI\SpotifyWebAPI;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

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
}
