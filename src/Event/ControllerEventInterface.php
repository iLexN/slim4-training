<?php

declare(strict_types=1);


namespace App\Event;

interface ControllerEventInterface
{
    /**
     * @return mixed
     */
    public function getArgs();


    /**
     * @param mixed $args
     */
    public function setArgs($args): void;

    public function getName(): string;
}
