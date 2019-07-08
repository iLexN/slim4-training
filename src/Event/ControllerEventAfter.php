<?php

declare(strict_types=1);

namespace App\Event;

final class ControllerEventAfter implements ControllerEventInterface
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
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @param array $args
     */
    public function setArgs(array $args): void
    {
        $this->args = $args;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'after';
    }
}
