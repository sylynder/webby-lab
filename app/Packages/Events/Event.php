<?php

namespace App\Packages\Events;

use App\Packages\Events\Exceptions\ArgumentCountException;
use App\Packages\Events\Exceptions\ClassNotFoundException;

/**
 * Event
 *
 * Simply Observe - Subscribe and Listen for Broadcasted Events.
 *
 * @package Event
 * @author Colin Rafuse <colin.rafuse@gmail.com>
 */
class Event
{
    /**
     * @var object
     */
    protected $app;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $subscriptions = [];

    /**
     * Create an instance of Event.
     *
     * @return Event
     */
    public function __construct()
    {
        // Get the CodeIgnitor Instance
        $this->app = app();

        // Load the Event config file located in application/config/event.php
        // An example file can be found in Config/event.php
        // $this->app->config->load('Events/Event', true);

        // Subscribe listeners to events via the config file, if any have been set
        // $this->config['subscriptions'] = config('subscriptions');
        $this->config['subscriptions'] = $this->app->config->item('subscriptions');
        
        if (is_array($this->config['subscriptions'])) {
            $this->subscribeFromConfig();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Event Broadcast
    |--------------------------------------------------------------------------
    |
    | Handle the Event Broadcast.
    |
    */
    /**
     * Broadcast a Event Event.
     *
     * @param   string      $event
     * @param   array       $arguments
     * @return  void
     */
    public function broadcast(string $event, ...$arguments): void
    {
        // dd($this->subscriptions);
        // Is this event subscribed to? If not, just return and don't fire
        if (!array_key_exists($event, $this->subscriptions)) {
            return;
        }

        // dd($this->subscriptions);

        // Construct the event class via Reflection
        $event_class = $this->constructReflectionClass($event);

        // Validate the event class constructor argument count against the provided argument count
        // Throw an exception if the count does not equal
        if (count($event_class->getConstructor()->getParameters()) !== count($arguments)) {
            throw new ArgumentCountException('The argument count is incorrect for the ' . $event . ' class constructor.');
        }

        // Create a new event class instance and pass the provided arguments
        $event_instance = $event_class->newInstance(...array_values($arguments));

        // Fire the registered listeners for the event
        $this->fire($event_class, $event_instance);
    }

    /*
    |--------------------------------------------------------------------------
    | Event Subscriptions
    |--------------------------------------------------------------------------
    |
    | Handle the Event Subscriptions.
    |
    */
    /**
     * Subscribe the Event Listeners.
     *
     * @param   string      $event
     * @param   mixed       $listeners
     * @return  void
     */
    public function subscribe(string $event, $listeners): void
    {
        // dd($event, $listeners);
        if (gettype($listeners) === 'string') {
            $this->subscriptions[$event][] = $listeners;
        } elseif (gettype($listeners) === 'array') {
            foreach ($listeners as $listener) {
                $this->subscriptions[$event][] = $listener;
            }
        }
    }

    /**
     * Subscribe the Event Listeners from the Event configuration file.
     *
     * @return  void
     */
    private function subscribeFromConfig(): void
    {
        foreach ($this->config['subscriptions'] as $event => $listeners) {
            $this->subscribe($event, $listeners);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Event Listeners
    |--------------------------------------------------------------------------
    |
    | Handle the Event Listeners.
    |
    */
    /**
     * Fire the Subscribed Listeners for the given Event.
     *
     * @param   mixed      $event_class
     * @param   mixed      $event_instance
     * @return  void
     */
    private function fire($event_class, $event_instance): void
    {
        foreach ($this->subscriptions[$event_class->getName()] as $listener) {
            $listener_class = null;
            $listener_run_method = null;

            // Construct the listener class via Reflection
            $listener_class = $this->constructReflectionClass($listener);

            // Invoke the Listener
            if (in_array('TFHInc\\Echelon\\Traits\\EchelonQueueListener', $listener_class->getTraitNames()) === true) {
                $listener_run_method = new \ReflectionMethod($listener, 'queue');
                $listener_run_method->invoke(new $listener, $event_instance);
            } else {
                $listener_run_method = new \ReflectionMethod($listener, 'run');
                $listener_run_method->invoke(new $listener, $event_instance);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Event Utilities
    |--------------------------------------------------------------------------
    |
    | Event Utility Methods.
    |
    */
    /**
     * Construct a class via Reflection.
     *
     * @param   string      $class_name
     * @return  mixed
     */
    private function constructReflectionClass($class_name)
    {
        try {
            return new \ReflectionClass($class_name);
        } catch (\ReflectionException | \Exception $e) {
            throw new ClassNotFoundException('The class ' . $class_name . ' does not exist and cannot be constructed.');
        }
    }
}
