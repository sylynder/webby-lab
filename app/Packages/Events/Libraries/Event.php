<?php

use App\Packages\Events\Event as EventInstance;

/**
 * Event
 *
 * A wrapper library to load Event.
 *
 * @package Event
 * @author Colin Rafuse <colin.rafuse@gmail.com>
 */
class Event extends EventInstance
{
    /**
     * Class constructor
     *
     * @return    void
     */
    public function __construct() {
        parent::__construct();
    }
}
