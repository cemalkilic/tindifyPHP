<?php


namespace App\Controller;


use SpotifyWebAPI\SpotifyWebAPI;
use SpotifyWebAPI\SpotifyWebAPIException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SongController extends AbstractController {

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

    public function getSong(Request $request) {
        $res = [
            "success" => false,
            "message" => "Song details",
            "content" => []
        ];

        $songID = $request->attributes->get('id', null);

        try {
            $res['content'] = $this->api->getTrack($songID);
            $res['success'] = true;
        } catch (SpotifyWebAPIException $e) {
            $res['message'] = $e->getReason() ?? $e->getMessage();
        }

        return JsonResponse::create($res);
    }

}
