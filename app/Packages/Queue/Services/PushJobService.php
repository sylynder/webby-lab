<?php

use Base\Console\ConsoleColor;
use Base\Services\Service;

class PushJobService extends Service
{
    public function __construct()
    {
        $this->use->model('Queue/QueueModel');
        $this->use->service('Frog/PushNotificationsService');

    }

    // public function process($type, $limit=10){}

    public function processFrogEmail($limit = 1, $attempts = 3)
    {
        $notify = $this->PushNotificationsService;

        $pushNotifications = $this->QueueModel->getPushNotificationBatch(0, $limit, $attempts);
        
        $startTime = microtime(true);

        $totalPushs = $pushNotifications;
        $update = [];
        $success = false;
        
        if (count($totalPushs) < 1) {
            return true;
        }

        $totalSteps = $totalPushs;
        $currentStep   = 0;
        $success = true;

        // if (is_cli()) {
        //     ConsoleColor::green('Emails to send: '.$totalSteps);
        // }

        foreach($pushNotifications as $notifications) {
            
            $msg = (object)$notifications;
            
            $payload = json_decode($msg->payload);

            // $sent = $notify->emailDestinations($notifications)
            //     ->prepareEmail($payload->from, $payload->title, $payload->body)
            //     ->sendEmail();

            // if ($sent) {

            //     $update = [
            //         'status' => QueueStatus::SENT, 
            //         'sent' => 1,
            //         'attempts'=> ($msg->attempts + ONE),
            //         'run_time' => (float) number_format((microtime(true) - $startTime), 4),
            //         'sent_at' => datetime()
            //     ];

            //     $log_subject = $payload->title;
            //     $log_message = $payload->body;
            //     $log_email = !is_array($payload->emails) ? $payload->emails : arrtostr(',', $payload->emails);

            //     log_message('user', "Sent SMS Log:=> Sent To: {$log_email} | Subject: {$log_subject} | Message: {$log_message} ");

            //     $success = true;
            // }

            // if (!$sent) {

            //     $update = [
            //         'status' => QueueStatus::FAILED,
            //         'attempts'=> ($msg->attempts + ONE),
            //         'run_time' => (float) number_format((microtime(true) - $startTime), 4),
            //     ];

            //     $log_subject = $payload->title;
            //     $log_message = $payload->body;
                
            //     $log_email = !is_array($payload->emails) ? $payload->to : arrtostr(',', $payload->emails);

            //     log_message('user', "Failed SMS Log:=> Sent To: {$log_email} | Subject: {$log_subject} | Message: {$log_message} ");

            //     $success = false;
            // }

            // $this->QueueModel->simpleUpdate(['id' => $msg->id], $update);

            // return $success;
        }

    }

}
