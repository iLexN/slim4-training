<?php

declare(strict_types=1);

namespace App\Event;

use Psr\EventDispatcher\StoppableEventInterface;

final class ControllerEvent implements StoppableEventInterface
{
    /**
     * @var array
     */
    private $args;

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
     * Is propagation stopped?
     *
     * This will typically only be used by the Dispatcher to determine if the
     * previous listener halted propagation.
     *
     * @return bool
     *   True if the Event is complete and no further listeners should be
     *     called. False to continue calling listeners.
     */
    public function isPropagationStopped(): bool
    {
        return true;
    }
}
