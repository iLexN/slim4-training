<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\ControllerEventBefore;

final class ControllerEventListener1
{
    public function __invoke(ControllerEventBefore $event): void
    {
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
