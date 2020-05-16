<?php


namespace App\Controller;


use App\Service\SpotifyAPIWrapper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BaseController extends AbstractController {

    protected $container;
    protected $api;

    public function __construct(ContainerInterface $container,
                                SpotifyAPIWrapper $api
    ) {
        $this->container = $container;
        $this->api = $api;
        $this->setupAPI();
    }

    public function setupAPI() {
        $this->api->setAccessToken($this->getUser()->getAccessToken());
        $this->api->setUsername($this->getUser()->getUsername());
    }

}
