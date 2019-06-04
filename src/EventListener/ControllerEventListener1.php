<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\ControllerEventBefore;
use Psr\Log\LoggerInterface;

final class ControllerEventListener1
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(ControllerEventBefore $controllerEventBefore): void
    {
        $this->logger->info('logger u');
        dump('here is controller event1');
        dump($controllerEventBefore->getArgs());
        $a = $controllerEventBefore->getArgs();
        if (isset($a['name'])) {
            $a['name'] .= ' (before)';
        }
        dump($controllerEventBefore->getName());
        $controllerEventBefore->setArgs($a);
    }
}
