<?php

use Base\Http\HttpCurl;
use Base\Services\Service;

#[\AllowDynamicProperties]
class FrogService extends Service
{
    private $username;
    private $password;
    private $senderId;
    private $service;
    private $smstype;
    private $apiUrl;
    private $model = 'Frog/FrogModel';
    private $configModel = 'Frog/FrogModel';
    private $response;

    public $useSenderId;
    public $keepLog = false;
    public $saveLog = false;
    public $useConfig = 'env';

    private const EMAIL_SERVICE = 'EMAIL';
    private const SMS_SERVICE = 'SMS';
    private const VOICE_SERVICE = 'VOICE';

    private const USER_AGENT = 'Frog Api Service';

    public function __construct()
    {
        $this->config = (object)$this->{$this->useConfig}();

        $this->username = $this->config->username;
        $this->password = $this->config->password;
        $this->senderId = $this->config->sender_id;
        $this->service  = $this->config->service;
        $this->smstype  = $this->config->smstype;
        $this->apiUrl   = $this->config->api_url;
        $this->keepLog  = $this->config->keep_log;
        $this->saveLog  = $this->config->save_log;
    }

    protected function env()
    {
        return [
            "username"  => env('frog.username'),
            "password"  => env('frog.password'),
            "sender_id" => $this->useSenderId ?: env('frog.main.senderid'),
            "service"   => env('frog.sms.service'),
            "smstype"   => env('frog.sms.type'),
            "api_url"   => env('frog.api.url'),
            "keep_log"  => env('frog.keep.log'),
            "save_log"  => env('frog.save.log')
        ];
    }

    protected function database()
    {
        $config = $this->dbconfig();

        return [
            "username"  => $config->username,
            "password"  => $config->password,
            "sender_id" => ($this->useSenderId) ? $this->useSenderId : $config->sender_id,
            "service"   => $config->service,
            "smstype"   => $config->smstype,
            "api_url"   => $config->api_url,
            "keep_log"  => (bool)$config->keep_log,
            "save_log"  => (bool)$config->save_log,
        ];
    }

    private function dbconfig()
    {
        return $this->frogConfigModel
            ->table($this->frogConfigModel->config)
            ->asObject()
            ->select()
            ->first();
    }

    protected function frogModel()
    {
        $this->use->model($this->model);
        return $this->FrogModel;
    }

    protected function frogConfigModel()
    {
        $this->use->model($this->configModel);
        return $this->FrogConfigModel;
    }

    /**
     * Save and Log Frog Response
     *
     * @param string $log
     * @return string|void
     */
    private function saveLog()
    {

        $log = json_decode($this->response);

        if ($log->status == 'ERROR') {
            return '';
        }

        $destinations = json_encode($log->destinations);

        $log = [
            'status'       => $log->status,
            'sender_id'    => $this->senderId,
            'batch_id'     => $log->batchid,
            'destinations' => $destinations,
            'reason'       => $log->reason,
            'service'      => $this->service,
            'created_at'   => datetime()
        ];

        $this->frogModel()->save($log);
    }

    /**
     * Call to log responses
     *
     * @param string $response
     * @return void
     */
    private function log($response = '')
    {

        $log = '';

        if (!empty($response) && is_json($response)) {

            $response = json_decode($response);

            if (($response->status == 'SUCCESS') || ($response->status == 'ACCEPTED')) {
                $log = 'Frog Response: SUCCESS: ' . $response->reason;
            } else {
                $log = 'Frog Response: ERROR: ' . $response->reason;
            }
            
        } else {
            $log = $response;
        }
        
        if ($this->keepLog) {
            log_message('app', $log);
        }

        if ($this->saveLog) {
            $this->saveLog($response);
        }

    }

    public function smsDestinations($numbers)
    {
        $list = [];

        if (!is_array($numbers)) {
            $numbers = [$numbers];
        }

        foreach ($numbers as $number) {
            $list[] = [
                'destination' => $number,
                'msgid' => unique_id(),
            ];
        }

        $this->destinations =  [
            "destinations" => $list
        ];

        return $this;
    }

    public function emailDestinations($emails)
    {
        $list = [];

        if (!is_array($emails)) {
            $emails = [$emails];
        }

        foreach ($emails as $email) {
            $list[] = [
                'destination' => $email,
                'msgid' => unique_id()
            ];
        }

        $this->destinations =  [
            "destinations" => $list
        ];

        return $this;
    }

    public function prepareSms(string $message)
    {
        $this->service = self::SMS_SERVICE;

        // Merge destinations with 
        // the needed default array
        $this->smsBody = array_merge([
            "username" => $this->username,
            "password" => $this->password,
            "senderid" => $this->senderId,
            "service"  => $this->service,
            "smstype"  => $this->smstype,
            "message"  => $message
        ], $this->destinations);

        return $this;
    }

    public function prepareEmail(
        string $from,
        string $subject,
        string $message
    ) {

        $this->service = self::EMAIL_SERVICE;

        // Merge destinations with 
        // the needed default array
        $this->emailBody = array_merge([
            "username"  => $this->username,
            "password"  => $this->password,
            "senderid"  => $this->senderId,
            "service"   => $this->service,
            "smstype"   => $this->smstype,
            "subject"   => $subject,
            "message"   => $message,
            "fromemail" =>  $from,
        ], $this->destinations);

        return $this;
    }

    public function sendSms(array $body = [])
    {
        $body = $body ?: $this->smsBody;

        $this->curl = new HttpCurl($this->apiUrl);

        $this->curl->userAgent = self::USER_AGENT;

        try {

            $this->curl->post('sendmsg', json_encode($body))
                ->option(CURLOPT_ENCODING, '')
                ->option(CURLOPT_MAXREDIRS, 10)
                ->option(CURLOPT_TIMEOUT, 0)
                ->option(CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1)
                ->option(CURLOPT_HTTPHEADER, ['Content-Type: application/json'])
                ->execute();

            if ($this->curl->hasError()) {
                $this->log('Frog Response: ERROR: ' . $this->curl->getErrorMessage());
                return false;
            }

            $response = $this->response = $this->curl->getLastResponse();

            $check = json_decode($response);

            if ($check->status == 'ERROR') {
                $this->log($this->response);
                return false;
            }

            $this->log($this->response);

            return true;

        } catch (Exception $e) {
            return false;
        }
    }

    public function sendSmsMessage($data)
    {

        // Prepare message to send
        $msg = (object)['title' => $data->title, 'body' => $data->message];

        $this->keepLog = true;

        // Send SMS
        $sent = $this
            ->smsDestinations($data->to)
            ->prepareSms($msg->body)
            ->sendSms();
        
        if ($sent) {

            $log_subject = $msg->title;
            $log_message = $msg->body;
            $log_phone = json_encode($data->to);

            log_message('user', "Sent SMS Log:=> Sent To: {$log_phone} | Subject: {$log_subject} | Message: {$log_message} ");

            return [
                'status' => true,
                'response' => "Sent successfully"
            ];
        }

        if (!$sent) {

            $log_subject = $msg->title;
            $log_message = $msg->body;
            $log_phone = json_encode($data->to);
            
            log_message('user', "Failed SMS Log:=> Sent To: {$log_phone} | Subject: {$log_subject} | Message: {$log_message} ");

            // echo "Sorry sms cannot be sent";

            return [
                'status' => false,
                'response' => "Message Was Not Sent"
            ];
        }
    }

    public function sendEmail(array $body = [])
    {

        $body = $body ?: $this->emailBody;

        $this->curl = new HttpCurl();
        $this->curl->create($this->apiUrl);

        $this->curl->userAgent = self::USER_AGENT;

        try {

            $this->curl->post('sendmsg', json_encode($body))
                ->option(CURLOPT_ENCODING, '')
                ->option(CURLOPT_MAXREDIRS, 10)
                ->option(CURLOPT_TIMEOUT, 0)
                ->option(CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1)
                ->option(CURLOPT_HTTPHEADER, ['Content-Type: application/json'])
                ->execute();

            if ($this->curl->hasError()) {
                $this->log('Frog Error: ' . $this->curl->getErrorMessage());
                return false;
            }

            $response = $this->curl->getLastResponse();

            if (!empty($response) && is_json($response)) {
                $response = json_decode($response);
            }

            if ($response->status == 'ERROR') {
                $this->log('Frog Response: ERROR: ' . $this->curl->getLastResponse());
                return false;
            }

            if ($response->status == 'SUCCESS') {
                $this->log('Frog Response: SUCCESS: ' . $this->curl->getLastResponse());
                return true;
            }

            return true;
        } catch (Throwable $th) {
            return false;
        }
    }
}
