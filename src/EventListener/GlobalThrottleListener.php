<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\HttpFoundation\Response;

class GlobalThrottleListener
{
    public function __construct(
        private RateLimiterFactory $globalApi
    )
    {}

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest())
        {
            return;
        }

        $limiter = $this->globalApi->create($event->getRequest()->getClientIp());

        if (!$limiter->consume(1)->isAccepted())
        {
            $event->setResponse(new Response(
                'Too many requests',
                Response::HTTP_TOO_MANY_REQUESTS
            ));
        }
    }
}