<?php

declare(strict_types=1);

namespace App\Event;

interface ControllerEventInterface
{
    /**
     * @return array|string[]
     */
    public function getArgs(): array;


    /**
     * @param array $args
     */
    public function setArgs(array $args): void;

    public function getName(): string;
}
