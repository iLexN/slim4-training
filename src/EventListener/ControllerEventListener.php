<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\ControllerEventInterface;

final class ControllerEventListener
{
    /**
     * interface order is after object event
     *
     * @param ControllerEventInterface $controllerEvent
     */
    public function __invoke(ControllerEventInterface $controllerEvent): void
    {
        dump('here is controller event');
        dump($controllerEvent->getArgs());
        $args = $controllerEvent->getArgs();
        if (isset($args['name'])) {
            $args['name'] .= ' (interface)' . $controllerEvent->getName();
        }
        $controllerEvent->setArgs($args);
    }
}
