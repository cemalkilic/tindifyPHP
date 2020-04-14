<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController {

    public function index() {

        // If user is authenticated, show the app homepage
        // Show landing page otherwise.
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->render("base.html.twig");
        } else {
            // TODO landing template here
            $content = '<a href="/auth"> Click here to login</a>';
            return new Response($content);
        }
    }

}
