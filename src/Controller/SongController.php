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

    public function getRecommendations(Request $request) {
        $res = [
            "success" => false,
            "message" => "Get recommended songs based on the given songs!",
            "content" => []
        ];

        $seedSongs = $request->request->get('seedSongs', null);
        if (empty($seedSongs)) {
            $res['message'] = "No seed songs givens!";
            return JsonResponse::create($res, 400);
        }

        if (!is_array($seedSongs)) {
            $res['message'] = "Seed songs should be given as an array of songs IDs!";
            return JsonResponse::create($res, 400);
        }

        $options = [
            "limit"       => 20, // number of returned songs
            "seed_tracks" => $seedSongs
        ];

        try {
            $res['content'] = $this->api->getRecommendations($options);
            $res['success'] = true;
        } catch (SpotifyWebAPIException $e) {
            $res['message'] = $e->getReason() ?? $e->getMessage();
        }

        return JsonResponse::create($res);
    }

}
