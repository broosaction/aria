<?php

namespace Core\Router\Event;



use Symfony\Component\EventDispatcher\EventDispatcher as SymfonyDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventDispatcher
{

    /** @var SymfonyDispatcher */
    private $dispatcher;



    public function __construct(SymfonyDispatcher $dispatcher) {
        $this->dispatcher = $dispatcher;
    }

    public function addListener(string $eventName,
                                callable $listener,
                                int $priority = 0): void {
        $this->dispatcher->addListener($eventName, $listener, $priority);
    }

    /**
     * Adds an event subscriber.
     *
     * The subscriber is asked for all the events it is
     * interested in and added as a listener for these events.
     */
    public function addSubscriber(EventSubscriberInterface $subscriber) {
        $this->dispatcher->addSubscriber($subscriber);
    }

    public function dispatch(string $eventName,
                             AriaEvent $event): void {
        $this->dispatcher->dispatch($event, $eventName);

    }

    public function dispatchTyped(AriaEvent $event): void {
        $this->dispatch(get_class($event), $event);
    }

    /**
     * @return SymfonyDispatcher
     */
    public function getSymfonyDispatcher(): SymfonyDispatcher {
        return $this->dispatcher;
    }

    public function removeListener($eventName, $listener) {
        $this->dispatcher->removeListener($eventName, $listener);
    }

    public function removeSubscriber(EventSubscriberInterface $subscriber) {
        $this->dispatcher->removeSubscriber($subscriber);
    }

    /**
     * Checks whether an event has any registered listeners.
     *
     * @param string|null $eventName The name of the event
     *
     * @return bool true if the specified event has any listeners, false otherwise
     */
    public function hasListeners($eventName = null) {
        return $this->dispatcher->hasListeners($eventName);
    }


}