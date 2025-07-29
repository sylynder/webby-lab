<?php

use App\Packages\Mail\Mailer;
use Base\Controllers\WebController;

class MailQueue extends WebController
{

    public function __construct()
    {
        parent::__construct();

        if (!input()->is_cli_request()){
            show_404();
        }

        $this->mailer = new Mailer;

    }

    public function index()
    {
        // Huh?
        show_404();
    }

    public function send_queue()
    {
        $this->mailer->sendQueue();
    }

    public function retry_queue()
    {
        $this->mailer->retryQueue();
    }

}
