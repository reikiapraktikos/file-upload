<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

final class TerminateSubscriber implements EventSubscriberInterface
{
    public function __construct(private RouterInterface $router, private FilesystemOperator $localStorage)
    {
    }

    /** @throws FilesystemException */
    public function onKernelTerminate(TerminateEvent $event): void
    {
        $request = $event->getRequest();
        $currentRoute = $this->router->match($request->getPathInfo());

        if ('upload' === $currentRoute['_route']) {
            $fileName = $request->attributes->get('fileName');

            if ($fileName !== null && $this->localStorage->fileExists($fileName)) {
                $this->localStorage->delete($fileName);
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::TERMINATE => 'onKernelTerminate',
        ];
    }
}
