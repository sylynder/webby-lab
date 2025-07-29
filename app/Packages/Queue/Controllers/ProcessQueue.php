<?php

// use Firebase\JWT\JWT;

use App\Events\Test;
use Base\Http\HttpStatus;
use Base\Helpers\Uuid;
use Base\Controllers\WebController;
use App\Models\TestModel;
use App\Packages\Mail\Mailable;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
  

class ProcessQueue extends WebController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $key = "example_key";

        $payload = array(
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => 1356999524,
            "nbf" => 1357000000
        );

    }

    public function queue()
    {

        $this->useDatabase();
        $this->use->helper('Queue/Status');
        $this->use->model('Queue/QueueModel');
        $this->use->service('Queue/QueueService');
        $this->use->service('Queue/SmsJobService');
        $this->use->service('Queue/EmailJobService');

        $this->queue = $this->QueueModel;
        $this->job = $this->QueueService;
        $this->smsJob = $this->SmsJobService;
        $this->emailJob = $this->EmailJobService;

        // dd($this->smsJob->processFrogSms(10, 4));
        dd($this->emailJob->processFrogEmail(10, 4));

        // $developer = 'Developer Kwame';
        // $message = 'You are doing well, and it is okay with that';
        // $phone_numbers = '0243721004';

        // // Prepare message to send
        // $msg = objectify([
        //     'title' => 'An Sms for signing up',
        //     'body' => $message
        // ]);

        // $payload = [
        //     'title' => $msg->title,
        //     'body' => $msg->body,
        //     'to' => $phone_numbers
        // ];

        // $developer = 'Developer Kwame';
        // $message = 'You are doing well, and it is okay with that';
        // $emails = ['otengkwameit@gmail.com', 'developerkwame@gmail.com'];

        // Prepare message to send
        // $msg = objectify([
        //     'title' => 'A Test by ' . $developer . ' on FROG\'s Email feature', 
        //     'body' => $message
        // ]);
        
        // $payload = [
        //     'from' => env('support.email'),
        //     'subject' => $msg->title,
        //     'body' => $msg->body,
        //     'emails' => $emails
        // ];

        // $queued = $this->queue->enqueue(Uuid::v4(), QueueType::FROGMAIL, $payload);

        // $queued = $this->queue->enqueue(Uuid::v4(), QueueType::FROGSMS, $payload);

        // if ($queued) {
        //     echo "Queued";
        // }

        // dd($this->job->processJob());
    }

}
