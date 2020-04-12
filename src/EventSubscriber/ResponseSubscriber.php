<?php


namespace App\EventSubscriber;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseSubscriber implements EventSubscriberInterface {

    public static function getSubscribedEvents() {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelResponse(ResponseEvent $event) {
        $response = $event->getResponse();

        if ($response instanceof JsonResponse) {
            $content = $response->getContent();

            $resp = [
                "content" => [],
            ];

            // We are expecting a error message(as string)
            // if returning a error code.
            // Put it in the 'errorDetails' field of response.
            // TODO use safer json_decode & json_encode!
            if ($response->isSuccessful()) {
                $resp['content'] = json_decode($content);
            } else {
                $resp['errorDetails'] = json_decode($content);
            }
            $response->setContent(json_encode($resp));
        }
    }

}
