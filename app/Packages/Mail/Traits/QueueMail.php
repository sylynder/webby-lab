<?php

namespace App\Packages\Mail\Traits;

trait QueueMail 
{

    /**
     * Queue table to use
     *
     * @var string
     */
    public $queueTable = 'mail_queue';

    // Main controller

    /**
     * Controller function to use
     *
     * @var string
     */
    public $withController = 'cron/queue/queue_mails'; //Cron/MailQueueController/sendPendingMails;

    /**
     * Route to use as alias
     *
     * @var string
     */
    public $withRoute = 'send.pending.mails';

    /**
     * Main Mail recipients
     *
     * @var array
     */
    public $recipients = [];

    /**
     * Carbon Copy recipients
     *
     * @var array
     */
    public $ccs = [];

    /**
     * Blind Carbon Copy recipients
     *
     * @var array
     */
    public $bccs = [];

    /**
     * Mail Body
     * Can contain html
     *
     * @var string
     */
    public $body = '';

    /**
     * Mail Content
     * Contains raw content
     * 
     * @var string
     */
    public $content = '';

    /**
     * Mail Headers
     *
     * @var array
     */
    public $headers = [];

    /**
     * Default Mail Template
     *
     * @var string
     */
    public $defaultTemplate = 'emails.default';

    /**
     * Queue status
     * Statuses (pending, sending, sent, failed)
     * @var string
     */
    public $status = 'pending';

    /**
     * Sending status
     *
     * @var string
     */
    public $sendStatus = 'sending';

    /**
     * Failed Status
     *
     * @var string
     */
    public $failStatus = 'failed';

    /**
     * Sent Status
     *
     * @var string
     */
    public $sentStatus = 'sent';

    /**
     * PHP Nohup command line
     *
     * @var string
     */
    private $phpcli = 'nohup php';

    /**
     * Queue Expiration
     *
     * @var int
     */
    public $expiration = 60 * 5;

    /**
     * Initialize Queue
     *
     * @param int $expiration
     * @return void
     */
    public function initQueue($expiration = 0)
    {
        log_message('debug', 'Email Queue Trait Initialized');

        if ($expiration !== 0) {
            $this->expiration = $expiration;
        }

        return $this;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return static
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Start process
     *
     * Start php process to send emails
     * @return  mixed
     */
    public function startProcess()
    {
        $filename = ROOTPATH . 'public/index.php';

        return shell_exec(
            // "{$this->phpcli} {$filename} {$this->withController} > /dev/null &"
            "{$filename} {$this->withController} > /dev/null &"
        );
    }

}
