<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\ControllerEventInterface;

final class ControllerEventListener
{
    /**
     * interface order is after object event
     *
     * @param \App\Event\ControllerEventInterface $event
     */
    public function __invoke(ControllerEventInterface $event): void
    {
        dump('here is controller event');
        dump($event->getArgs());
        $a = $event->getArgs();
        if (isset($a['name'])) {
            $a['name'] .= ' (interface)' . $event->getName();
        }
        $event->setArgs($a);
    }
}
