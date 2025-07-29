<?php

use Base\Models\EasyModel;

class QueueModel extends EasyModel
{
    /**
     * Queued Jobs Table
     *
     * @var string
     */
    public $table = 'queued_jobs';
    
    /**
     * Failed Jobs Table
     *
     * @var string
     */
    public $failedTable = 'failed_jobs';

    /**
     * Sent Status
     *
     * @var integer
     */
    private $sentStatus = 0; // 0 for not sent , 1 for sent

    /**
     * Sorting Order 
     *
     * @var string
     */
    public $sort = 'ASC';

    public function __construct()
    {
        parent::__construct();

        $this->use->helper('Queue/Status');

    }

    /**
     * Stores a new queue job
     *
     * @param string $name name of queue preferrably a uuid
     * @param string $type the type of queue, i.e.sms|email|push_notification|frog_sms|frog_email|task
     * @param array $payload associative array of variables to be passed
     * @param integer $attempts number of attempts until queue fails
     * @return bool
     */
    public function enqueue($name, $type, $payload, $attempts = 0)
    {
        $queued = null;

        switch($type) {
            case QueueType::FROGSMS:
                $queued = $this->queueFrogSms($name, $payload, $attempts);
            break;
            case QueueType::FROGMAIL:
                $queued = $this->queueFrogEmail($name, $payload, $attempts);
            break;
            case QueueType::PUSH_NOTIFICATION:
                $queued = $this->queueFrogEmail($name, $payload, $attempts);
            break;
            case QueueType::TASK:
                $queued = $this->queueTask($name, $payload, $attempts);
            break;
            default: 
                $queued = false;
        }
        
        // if ($type === QueueType::FROGMAIL) {
        //     $queued = $this->queueFrogEmail($name, $payload, $attempts);
        // }

        // if ($type === QueueType::FROGSMS) {
        //     $queued = $this->queueFrogSMS($name, $payload, $attempts);
        // }
        
        return (is_int($queued) && $queued >= 1 ) ? true : false;

    }

    /**
     * Returns a list of queued emails that needs to be sent.
     *
     * @param int $status sent or not
     * @param int|string $size number of unset emails to return
     * @return array list of unsent emails
     */
    public function getBatch($status = '0,1', $type = '', $size = 100, $attempts = 3): array
    {
        return $this
            ->whereIn('sent', explode(',', $status))
            ->where('attempts <=', $attempts)
            ->where('queue', $type)
            ->limit($size)
            ->orderBy('created_at', $this->sort)
            ->asArray()
            ->findAll();
    }

    public function getSmsBatch($status = '0,1', $size = 100, $attempts = 3, $type = QueueType::FROGSMS): array
    {
        return $this
            ->whereIn('sent', explode(',', $status))
            ->where('attempts <=', $attempts)
            ->where('queue', $type)
            ->limit($size)
            ->orderBy('created_at', $this->sort)
            ->asArray()
            ->findAll();
    }

    public function getMailBatch($status = '0,1', $size = 100, $attempts = 3, $type = QueueType::FROGMAIL): array
    {
        return $this
            ->whereIn('sent', explode(',', $status))
            ->where('attempts <=', $attempts)
            ->where('queue', $type)
            ->limit($size)
            ->orderBy('created_at', $this->sort)
            ->asArray()
            ->findAll();
    }

    public function getTask($name, $status = '0', $size = 100, $attempts = 3, $type = QueueType::TASK) 
    {
        return $this
            ->whereIn('sent', $status)
            ->where('attempts <=', $attempts)
            ->where('queue', $type)
            ->where('name', $name)
            ->limit($size)
            ->orderBy('created_at', $this->sort)
            ->asArray()
            ->findAll();
    }
    
    public function getPushNotificationBatch($status = '0,1', $size = 100, $attempts = 3, $type = QueueType::PUSH_NOTIFICATION) 
    {
        return $this
            ->whereIn('sent', explode(',', $status))
            ->where('attempts <=', $attempts)
            ->where('queue', $type)
            ->limit($size)
            ->orderBy('created_at', $this->sort)
            ->asArray()
            ->findAll();
    }


    public function queueTask($name, $params, $attempts = 0)
    {
        $params = (object)$params;

        return $this->save([
            'name' => $name,
            'queue' => QueueType::TASK,
            'payload' => json_encode($params),
            'attempts' => $attempts,
            'sent' => ZERO,
            'schedule_at' => (!isset($params->schedule)) ? travel()->to('2 minutes')->format() : $params->schedule, 
            'created_at' => datetime()
        ]);
        
    }

    public function queueFrogSms($name, $payload, $attempts = 0, $schedule = null)
    {
        $payload = is_object($payload)
            ? $payload
            : (object)($payload);

        return $this->save([
            'name' => $name,
            'queue' => QueueType::FROGSMS,
            'payload' => json_encode([
                'title' => $payload->title,
                'body' =>  $payload->body,
                'to' => $payload->to,
            ]),
            'attempts' => $attempts,
            'sent' => ZERO,
            // 'schedule_at' => travel()->to('5 secs')->format(),
            'schedule_at' => (!isset($payload->schedule)) ? travel()->to('5 secs')->format() : $payload->schedule, 
            'created_at' => datetime()
        ]);

    }
    
    public function queueFrogEmail($name, $payload, $attempts = 0, $schedule = null)
    {

        $payload = is_object($payload) 
            ? $payload 
            : (object)($payload);

        return $this->save([
            'name' => $name,
            'queue' => QueueType::FROGMAIL,
            'payload' => json_encode([
                'from' =>  $payload->from,
                'title' => $payload->subject,
                'body' =>  $payload->body,
                'emails' => $payload->emails,
            ]),
            'attempts' => $attempts,
            'sent' => ZERO,
            // 'schedule_at' => travel()->to('5 secs')->format(),
            'schedule_at' => (!isset($payload->schedule)) ? travel()->to('5 secs')->format() : $payload->schedule, 
            'created_at' => datetime()
        ]);

    }

    public function queuePushNotification($name, $payload, $attempts = 0, $schedule = null)
    {

        $payload = is_object($payload) 
            ? $payload 
            : (object)($payload);

        return $this->save([
            'name' => $name,
            'queue' => QueueType::PUSH_NOTIFICATION,
            'payload' => json_encode($payload),
            'attempts' => $attempts,
            'sent' => ZERO,
            // 'schedule_at' => travel()->to('5 secs')->format(),
            'schedule_at' => (!isset($payload->schedule)) ? travel()->to('5 secs')->format() : $payload->schedule, 
            'created_at' => datetime()
        ]);

    }

    public function queueSimpleMail($params, $receivers, $name = 'simple_mail')
    {
        $params = (object)$params;

        $receivers = (!is_array($receivers)) ?: [$receivers];

        foreach ($receivers as $receiver) {
            $this->save([
                'name' => $params->name,
                'queue' => QueueType::EMAIL,
                'payload' => json_encode([
                    'from' => $params->from,
                    'subject' => $params->subject,
                    'type' => $params->type,
                    'body' => $params->body,
                    'sendTo' => $receiver,
                ]),
                'created_at' => datetime()
            ]);
        }

        return true;
    }
    
    public function failedJob()
    {
        $failedJobs = $this->where('status', QueueStatus::FAILED)->get();

        $move = null;

        if ($failedJobs) {

            $this->table = $this->failedTable; // Set Table to failed_jobs
            
            foreach ($failedJobs as $failed) {
                $this->insert($failed);
            }

            $this->clearJob(QueueStatus::FAILED);
            
        }

        // return 
    }

    public function clearJob($status = QueueStatus::SENT)
    {
        return $this->delete('status', $status);
    }

    public function archiveJob($status = QueueStatus::SENT)
    {
        // return $this->clearJob();
    }
    
}
