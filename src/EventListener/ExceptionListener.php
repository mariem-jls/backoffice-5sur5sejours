<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $response = new JsonResponse([
            'error' => $exception->getMessage(),
        ]);

        if ($exception instanceof NotFoundHttpException) {
            $response->setStatusCode(404);
        } else {
            $response->setStatusCode(500);
        }

        $event->setResponse($response);
    }
}
