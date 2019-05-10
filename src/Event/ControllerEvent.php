<?php

declare(strict_types=1);


namespace App\Event;

final class ControllerEvent implements \Psr\EventDispatcher\StoppableEventInterface
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

    /**
     * Is propagation stopped?
     *
     * This will typically only be used by the Dispatcher to determine if the
     * previous listener halted propagation.
     *
     * @return bool
     *   True if the Event is complete and no further listeners should be called.
     *   False to continue calling listeners.
     */
    public function isPropagationStopped(): bool
    {
        return true;
    }
}
