<?php

declare(strict_types=1);

namespace App\Event;

final class ControllerEventBefore implements ControllerEventInterface
{
    /**
     * @var array
     */
    private $args = [];

    public function __construct(array $args)
    {
        $this->args = $args;
    }

    /**
     * @return mixed
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param mixed $args
     */
    public function setArgs($args): void
    {
        $this->args = $args;
    }

    public function getName(): string
    {
        return 'before';
    }
}
