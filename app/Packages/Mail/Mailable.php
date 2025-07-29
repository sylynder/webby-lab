<?php

namespace App\Packages\Mail;

use App\Packages\Mail\Mailer;
use App\Packages\Mail\Configuration;

class Mailable
{

    /**
     * Mail variable
     *
     * @var object
     */
    private static $mail;

    /**
     * Configuration variable
     *
     * @var array
     */
    private static $configuration = [];

    /**
     * Set Html true
     *
     * @var boolean
     */
    public static $isHtml = true;

    // private function checkEmailValiditity($email) 
    // {

    // }

    /**
     * Is Email Exists
     *
     * @param string $email
     * @return boolean
     */
    public static function isEmailExists($email)
    {
        return (new Mailer)->emailReallyExists($email);
    }

    /**
     * Set Configuration
     *
     * @param Configuration $config
     * @return mixed
     */
    public static function setConfiguration(Configuration $config)
    {
        static::$configuration = $config->defaultConfig();
        return  static::$configuration;
    }

    /**
     * Get Configuration
     *
     * @return mixed
     */
    public static function getConfiguration()
    {
        return (new Configuration)->defaultConfig();
    }

    /**
     * Create Mail
     *
     * @return mixed
     */
    public static function createMail()
    {
        static::$mail = new Mailer();
        static::$mail->config = static::getConfiguration();
        return static::$mail->initializeMail();
    }

    /**
     * Use CI Mail
     *
     * @return mixed
     */
    public static function useCiMail()
    {
        static::$mail = new Mailer();
        static::$mail->config = static::getConfiguration();
        return static::$mail->useCiMail();
    }

    /**
     * Send Mail
     *
     * @param mixed $mailData
     * @return mixed
     */
    public static function sendMail($mailData)
    {
        $mailData = (object)$mailData;

        static::$mail = new Mailer();
        static::$mail->config = static::getConfiguration();
        return static::$mail->initializeMail()
                ->setEmailFrom($mailData->from)
                ->setNameFrom($mailData->fromName)
                ->mailTo($mailData->sendTo)
                ->isHTML(static::$isHtml)
                ->setSubjectBody($mailData->subject, $mailData->mailBody)
                ->sendMail();
    }

    /**
     * Send Subscription Mail
     *
     * @param mixed $mailData
     * @return mixed
     */
    public static function sendSubscriptionMail($mailData)
    {
        return static::sendMail($mailData);
    }

    /**
     * Send System Mail
     *
     * @param mixed $mailData
     * @return mixed
     */
	public static function sendSystemMail($mailData)
    {
        if (
            empty(env('app.email') && empty(env('app.name')))
        ) {
            throw new \Exception('Make sure to set app.email and app.name in .env file');
        }

        static::$mail = new Mailer();
        static::$mail->config = static::getConfiguration();
        return static::$mail->initializeMail()
                ->setEmailFrom(env('app.email'))
                ->setNameFrom(env('app.name'))
                ->mailTo($mailData->sendTo)
                ->isHTML(static::$isHtml)
                ->setSubjectBody($mailData->subject, $mailData->mailBody)
                ->sendMail();
    }

    /**
     * Send Verification Success Mail
     *
     * @param mixed $mailData
     * @return mixed
     */
    public static function sendVerificationSuccessMail($mailData)
    {
        return static::sendSystemMail($mailData);
    }
 
    /**
     * Queue Mail
     *
     * @param string $to
     * @param string $subject
     * @param array $mailData
     * @param string $mailTemplate
     * @return mixed
     */
    public static function queueMail(
        $to = '', 
        $subject = '', 
        $mailData = [],
        $mailTemplate = 'emails.default'
    ) {
        $mailer  = new Mailer();
        $mailer->recipients = $to;
        $mailer->content = json_encode($mailData);

        return $mailer
                    ->useDb()
                    ->queue($subject, $mailTemplate);
    }

    /**
     * Schedule Mail
     *
     * @param string $to
     * @param string $subject
     * @param array  $mailData
     * @param string $mailTemplate
     * @param string $cc
     * @param string $bcc
     * @param string $attachment
     * @return mixed
     */
    public static function scheduleMail(
        $to = '', 
        $subject = '', 
        $mailData = [], 
        $mailTemplate = 'emails.default',
        $cc = '', 
        $bcc = '', 
        $attachment = ''
    ) {
        $mailer  = new Mailer();
        $mailer->recipients = $to;
        $mailer->ccs = $cc;
        $mailer->bccs = $bcc;
        // $mailer->attachment = $attachment;
        $mailer->content = json_encode($mailData);
        
        return $mailer
            ->useDb()
            ->queue($subject, $mailTemplate);
    }

}
