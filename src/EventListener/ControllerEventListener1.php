<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\ControllerEventBefore;
use Psr\Log\LoggerInterface;

final class ControllerEventListener1
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(ControllerEventBefore $event): void
    {
        $this->logger->info( 'logger u');
        dump('here is controller event1');
        dump($event->getArgs());
        $a = $event->getArgs();
        if (isset($a['name'])) {
            $a['name'] .= ' (before)';
        }
        dump($event->getName());
        $event->setArgs($a);
    }
}
