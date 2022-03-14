<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Enum\ExceptionMessage;
use JsonException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

final class ExceptionSubscriber implements EventSubscriberInterface
{
    /** @throws JsonException */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $response = new JsonResponse();

        if (!$exception instanceof HttpExceptionInterface) {
            $response->setContent(
                json_encode(
                    [
                        'message' => ExceptionMessage::DEFAULT->value,
                        'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    ],
                    JSON_THROW_ON_ERROR
                )
            );
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        } else {
            $statusCode = $this->getStatusCode($exception);
            $response->setStatusCode($statusCode);
            $response->setContent($this->getErrorMessage($exception, $statusCode));
        }

        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    private function getStatusCode(Throwable $exception): int
    {
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        }

        return $statusCode;
    }

    /** @throws JsonException */
    private function getErrorMessage(Throwable $exception, int $statusCode): string
    {
        return json_encode(
            [
                'message' => $exception->getMessage(),
                'status' => $statusCode,
            ],
            JSON_THROW_ON_ERROR
        );
    }
}
