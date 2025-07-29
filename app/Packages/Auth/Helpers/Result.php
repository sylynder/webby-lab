<?php

namespace App\Packages\Auth\Helpers;

#[\AllowDynamicProperties]
class Result 
{

    /**
     * The CodeIgniter object variable
     * @access public
     * @var object
     */
    public $app;

    /**
     * Variable for $status
     *
     * @var boolean
     */
    public bool $status = false;

    /**
     * Provides a simple explanation of
     * the error that happened.
     * Typically a single sentence.
     */
    protected ?string $reason = null;

    /**
     * Variable for loading the config array into
     * @access public
     * @var array
     */
    public $authSession = true;

    /**
     * Array to store error messages
     * @access public
     * @var array
     */
    public $errors = [];

    /**
     * Array to store info messages
     * @access public
     * @var array
     */
    public $infos = [];

    /**
     * Local temporary storage for current flash errors
     *
     * Used to update current flash data list since flash data is only available on the next page refresh
     * @access public
     * var array
     */
    public $flashErrors = [];

    /**
     * Local temporary storage for current flash infos
     *
     * Used to update current flash data list since flash data is only available on the next page refresh
     * @access public
     * var array
     */
    public $flashInfos = [];

        /**
     * Array to store userdata
     * 
     * @var array
     */
    public array $userdata = [];

    /**
     * Extra information.
     *
     * @var string|User|null `User` when successful. Suggestion strings when fails.
     */
    protected $extraInfo;

    /**
     * Constructor
     */
    public function __construct($useSession = true)
    {
        // get main ci object
        $this->app = &get_instance();

        $this->app->use->library('driver');
        $this->app->use->library('session');

        $this->errors = [];
        $this->infos = [];

        $this->authSession = $useSession;

        // if sessions are been used
        if ($this->authSession) {
            $this->app->use->library('session');
            $this->currentUserId = $this->app->session->userdata('user_id');

            // load error and info messages from flashdata 
            // (but don't store back in flashdata)
            $this->errors = $this->app->session->flashdata('errors') ?: [];
            $this->infos = $this->app->session->flashdata('infos') ?: [];
        }

        $this->app->lang->load('Auth/authy');

    }

    /**
     * Was the result a success?
     *
     * @return boolean
     */
    public function isOk(): bool
    {
        return $this->status;
    }

    /**
     * Get reason
     *
     * @return string|null
     */
    public function reason(): ?string
    {
        return $this->reason;
    }

    /**
     * @return string|object|null `User` when successful. Suggestion strings when fails.
     */
    public function extraInfo()
    {
        return $this->extraInfo;
    }

    /*----------------------- Error / Info Functions ---------------------------*/

    /**
     * Error
     * Add message to error array and set flash data
     * @param string $message Message to add to array
     * @param boolean $flashdata if true add $message to ci flashdata (deflault: false)
     */
    public function error($message = '', $flashdata = false)
    {
        $this->errors[] = $message;

        if ($flashdata) {
            $this->flashErrors[] = $message;
        }

        if ($this->authSession) {
            $this->app->session->set_flashdata('errors', $this->flashErrors);
        }
    }

    /**
     * Keep Errors
     *
     * Keeps the flashdata errors for one more page refresh.  Optionally adds the default errors into the
     * flashdata list.  This should be called last in your controller, and with care as it could continue
     * to revive all errors and not let them expire as intended.
     * Benefitial when using Ajax Requests
     * @see http://ellislab.com/codeigniter/user-guide/libraries/sessions.html
     * @param boolean $includeNonFlash true if it should show basic errors as flashdata (default = false)
     */
    public function keepErrors($includeNonFlash = false)
    {
        // NOTE: keep_flashdata() overwrites anything new that has been added to flashdata so we are manually reviving flash data
        // $this->app->session->keep_flashdata('errors');

        if ($includeNonFlash) {
            $this->flashErrors = array_merge($this->flashErrors, $this->errors);
        }

        $this->flashErrors = array_merge($this->flashErrors, (array) $this->app->session->flashdata('errors'));

        if ($this->authConfig['use.sessions']) {
            $this->app->session->set_flashdata('errors', $this->flashErrors);
        }
    }

    /**
     * Get Errors Array
     * Return array of errors
     * @return array Array of messages, empty array if no errors
     */
    public function getErrorsArray()
    {
        return $this->errors;
    }

    /**
     * Print Errors
     *
     * Prints string of errors separated by delimiter
     * @param string $divider Separator for errors
     */
    public function printErrors($divider = '<br />')
    {
        $msg = '';
        $msgNumber = count($this->errors);
        $i = 1;

        foreach ($this->errors as $e) {
            $msg .= $e;

            if ($i != $msgNumber) {
                $msg .= $divider;
            }

            $i++;
        }

        return $msg;
    }

    /**
     * Clear Errors
     *
     * Removes errors from error list and clears all associated flashdata
     */
    public function clearErrors()
    {
        $this->errors = [];

        if ($this->authSession) {
            $this->app->session->set_flashdata('errors', $this->errors);
        }

    }

    /**
     * Info
     *
     * Add message to info array and set flash data
     *
     * @param string $message Message to add to infos array
     * @param boolean $flashdata if true add $message to ci flashdata (deflault: false)
     */
    public function info($message = '', $flashdata = false)
    {
        $this->infos[] = $message;

        if ($flashdata) {
            $this->flashInfos[] = $message;
        }

        if ($this->authSession) {
            $this->app->session->set_flashdata('infos', $this->flashInfos);
        }
    }

    /**
     * Keep Infos
     *
     * Keeps the flashdata infos for one more page refresh.  Optionally adds the default infos into the
     * flashdata list.  This should be called last in your controller, and with care as it could continue
     * to revive all infos and not let them expire as intended.
     * Benefitial by using Ajax Requests
     * @see http://ellislab.com/codeigniter/user-guide/libraries/sessions.html
     * @param boolean $includeNonFlash true if it should stow basic infos as flashdata (default = false)
     */
    public function keepInfos($includeNonFlash = false)
    {
        // NOTE: keep_flashdata() overwrites anything new that has been added to flashdata so we are manually reviving flash data
        // $this->app->session->keep_flashdata('infos');

        if ($includeNonFlash) {
            $this->flashInfos = array_merge($this->flashInfos, $this->infos);
        }

        $this->flashInfos = array_merge($this->flashInfos, (array) $this->app->session->flashdata('infos'));
        
        if ($this->authSession) {
            $this->app->session->set_flashdata('infos', $this->flashInfos);
        }
    }

    /**
     * Get Info Array
     *
     * Return array of infos
     * @return array Array of messages, empty array if no errors
     */
    public function getInfosArray()
    {
        return $this->infos;
    }

    /**
     * Print Info
     *
     * Print string of info separated by delimiter
     * @param string $divider Separator for info
     *
     */
    public function printInfos($divider = '<br />')
    {
        $msg = '';
        $msgNumber = count($this->infos);
        $i = 1;

        foreach ($this->infos as $e) {
            $msg .= $e;

            if ($i != $msgNumber) {
                $msg .= $divider;
            }

            $i++;
        }
        echo $msg;
    }

    /**
     * Clear Info List
     *
     * Removes info messages from info list and clears all associated flashdata
     */
    public function clearInfos()
    {
        $this->infos = [];

        if ($this->authSession) {
            $this->app->session->set_flashdata('infos', $this->infos);
        }
    }

}
