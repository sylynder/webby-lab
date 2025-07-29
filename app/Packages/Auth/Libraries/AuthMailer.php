<?php

use App\Packages\Mail\Mailable;

/**
 * This is mail library to 
 * allow sending of emails
 * 
 * @author Oteng Kwame Appiah-Nti
 */
#[AllowDynamicProperties]
class AuthMailer
{
    /**
     * The CodeIgniter object variable
     * @access public
     * @var object
     */
    public $ci;

    /**
     * Local temporary storage for current flash infos
     *
     * Used to update current flash data list since flash data is only available on the next page refresh
     * @access public
     * var array
     */
    public $flashInfos = [];

    /**
     * Variable for loading the config array into
     * @access public
     * @var array
     */
    public $authConfig;

    /**
     * The CodeIgniter db variable
     * @access public
     * @var object
     */
    public $authDb;

    public const PHPMAILER = 'PHPMailer';

    /**
     * Constructor
     */
    public function __construct()
    {
        // get main ci object
        $this->ci = &get_instance();

        $this->ci->load->library('driver');
        $this->ci->load->library('session');

        $this->ci->load->config('Auth/Database');
        $this->ci->load->config('Auth/Auth');

        $this->authConfig = $this->ci->config->item('auth');
        $this->dbConfig = $this->ci->config->item('authy_db');

        $this->authDb = $this->ci->load->database($this->dbConfig, true);

        $this->confirmMailServiceAvailability($this->authConfig['email.service']);

        if (isset($this->authConfig['email.config']) && is_array($this->authConfig['email.config'])) {
            $this->ci->email->initialize($this->authConfig['email.config']);
        }

    }
    
    private function confirmMailServiceAvailability($service)
    {
        if (
            $service === self::PHPMAILER
            && !$this->isMailServiceAvailable()
        ) {
            throw new \Exception('Webby Mail Package is not available');
        }
    }

    private function isMailServiceAvailable()
    {
        return class_exists(App\Packages\Mail\Mailer::class) ? true : false;
    }

    // public function initializePHPMailer(array $config)
    // {
    //     return Mailable::setConfiguration($config);
    // }

    public function setBody($view, $contentPlaceholders)
    {
        return $this->ci->load->view($view, $contentPlaceholders, true);
    }

    public function sendMail($mailData)
    {
        return Mailable::sendMail($mailData);
    }

    public function queueMail(array $mail)
    {
        $mail = (object) ($mail);
        
        return Mailable::queueMail(
            $mail->sendTo,
            $mail->subject,
            $mail->placeholders,
            $mail->template
        );
    }

    public function sendNotificationMail($mailData)
    {
        return $this->sendMail($mailData);
    }

    public function sendVerificationMail($mailData)
    {
        return Mailable::sendSystemMail($mailData);
    }

    /**
     * Use CI Mail class
     *
     * @param object $mailData
     * @return bool
     */
    public function sendAsCiMail($mailData)
    {
        $mail = Mailable::useCiMail();
        $mail->from($mailData->from, $mailData->fromName);
        $mail->to($mailData->sendTo);
        $mail->subject($mailData->subject);
        $mail->message($mailData->message);
        
        try {
            $mail->send();
            return true;
        } catch(Throwable $throw){
            return false;
        }
    }
}
