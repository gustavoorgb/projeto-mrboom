<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AccessDeniedSubscriber implements EventSubscriberInterface {

    public function __construct(
        private RouterInterface $router,
        private RequestStack $requestStack
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void {
        $exception = $event->getThrowable();

        if (!$exception instanceof AccessDeniedHttpException and !$exception instanceof AccessDeniedException) {
            return;
        }

        $session = $this->requestStack->getSession();

        if ($session) {
            $session->getFlashBag()->add('danger', 'Você não tem permissão para acessar este recurso.');
        }

        $event->setResponse(
            new RedirectResponse($this->router->generate('app_index'))
        );
    }

    public static function getSubscribedEvents(): array {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
