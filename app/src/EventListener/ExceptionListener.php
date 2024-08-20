<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class ExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();

        // TODO - check DEV environment for show details and Hide for Production
        // var_dump($exception->getMessage());

        $headers = $exception->getHeaders();
        $headers['Content-Type'] = 'application/json';
        $message = [
            'error' => $exception->getMessage(),
            //'code' => $exception->getCode(),
        ];

        // Customize your response object to display the exception details
        $response = new JsonResponse();
        $response->setContent(json_encode($message));

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($headers);
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->headers->replace($exception->getHeaders());
            $response->setContent('');
        }

        $event->setResponse($response);
    }
}
