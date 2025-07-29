<?php

namespace App\Packages\Events;

/**
 * Event Listener
 *
 * An abstract class for the Event Listener classes.
 *
 * @package Event
 */
abstract class EventListener {
    /**
     * The abstract 'run()' method.
     *
     * @return  void
     */
    abstract public function run($event): void;
}
