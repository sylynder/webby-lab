<?php 

use Base\Actions\Action;

class NotificationAction extends Action 
{
    private $frog;
    private $appName;
    private $appEmail;

    public function __construct()
    {
        // load models, libraries, other actions etc here
        $this->use->helper('Events/Event');
        $this->use->service('Frog/FrogService');

        event()->subscribe('App\Events\UserRegisteredEvent', [
            'App\Listeners\UserRegisteredListener',
        ]);

    }

    /**
     * Broadcasts the user registered event.
     *
     * @param mixed $details The details of the user registration.
     */
    public function userRegistered($details)
    {
        $details = (object) $details;

        event()->broadcast('App\Events\UserRegisteredEvent', $details);
        
    }

    public function sendRegisterEmail($details)
    {

    }
    

}
/* End of NotificationAction file */
