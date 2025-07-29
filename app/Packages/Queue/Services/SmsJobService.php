<?php

use Base\Console\ConsoleColor;
use Base\Services\Service;

class SmsJobService extends Service
{
    public function __construct()
    {
        $this->use->model('Queue/QueueModel');
        $this->use->service('Frog/FrogService');
    }

    public function processFrogSms($limit = 10, $attempts = 3)
    {
        $frog = $this->FrogService;

        $sms = $this->QueueModel->getSmsBatch(0, $limit, $attempts);
        
        $startTime = microtime(true);

        $totalSms = $sms;
        $update = [];
        $success = false;
        
        if (count($totalSms) < 1) {
            echo ConsoleColor::green('No sms to send') . PHP_EOL;
            return true;
        }

        $totalSteps = count($totalSms);
        $currentStep   = 1;
        $success = true;

        if (is_cli()) {
            echo ConsoleColor::green('Sms to send: '.$totalSteps) . PHP_EOL;
        }

        foreach($sms as $message) {
            
            $msg = (object)$message;
            
            $payload = json_decode($msg->payload);

            $sent = $frog->smsDestinations($payload->to)
                ->prepareSms($payload->body)
                ->sendSms();

            if ($sent) {

                $update = [
                    'status' => QueueStatus::DONE, 
                    'sent' => 1,
                    'attempts'=> ($msg->attempts + ONE),
                    'run_time' => (float) number_format((microtime(true) - $startTime), 4),
                    'sent_at' => datetime()
                ];

                $log_subject = $payload->title;
                $log_message = $payload->body;
                $log_phone = !is_array($payload->to) ? $payload->to : arrtostr(',', $payload->to);

                log_message('user', "Sent SMS Log:=> Sent To: {$log_phone} | Subject: {$log_subject} | Message: {$log_message} ");

                $success = true;
            }

            if (!$sent) {

                $update = [
                    'status' => QueueStatus::FAILED,
                    'attempts'=> ($msg->attempts + ONE),
                    'run_time' => (float) number_format((microtime(true) - $startTime), 4),
                ];

                $log_subject = $payload->title;
                $log_message = $payload->body;
                
                $log_phone = !is_array($payload->to) ? $payload->to : arrtostr(',', $payload->to);

                log_message('user', "Failed SMS Log:=> Sent To: {$log_phone} | Subject: {$log_subject} | Message: {$log_message} ");

                $success = false;
            }

            $this->QueueModel->simpleUpdate(['id' => $msg->id], $update);

            if (is_cli()) {
                // CLI::showProgress($currentStep++, $totalSteps);
                echo ConsoleColor::green('Sms current step:' . $currentStep++ . ' total steps: ' .  $totalSteps) . PHP_EOL;
            }
        }

        return $success;

    }

}
