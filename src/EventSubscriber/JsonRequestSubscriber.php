<?php


namespace App\EventSubscriber;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class JsonRequestSubscriber
 *
 * Used for parsing JSON content to request body
 * @package App\Service
 */
class JsonRequestSubscriber implements EventSubscriberInterface {

    public static function getSubscribedEvents() {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event) {
        $request = $event->getRequest();
        $content = $request->getContent();

        if (empty($content)) {
            return;
        }

        if (!$this->isJsonRequest($request)) {
            return;
        }

        if (!$this->transformJSONBody($request)) {
            $response = Response::create('Unable to parse request.', 400);
            $event->setResponse($response);
        }
    }

    private function isJsonRequest(Request $request) {
        return strpos($request->headers->get('Content-Type'), 'application/json') === 0;
    }

    private function transformJSONBody(Request $request) {
        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        if ($data === null) {
            return true;
        }

        $request->request->replace($data);
        return true;
    }
}
