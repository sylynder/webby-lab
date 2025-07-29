<?php


if ( ! function_exists('event')) 
{
    /**
     * Return an instance of the Events Library.
     *
     * @return \App\Packages\Events\Event
     */
    function event(): \App\Packages\Events\Event
    {
        app()->use->library('Events/Event', null, 'event');

        return app()->event ??= new \App\Packages\Events\Event();
    }
}
