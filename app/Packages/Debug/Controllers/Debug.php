<?php

use App\Packages\Debug\Middleware\DebugMiddleware;

class Debug extends DebugMiddleware
{
    private $logPath = WRITABLEPATH . 'logs/system/';
    
    private $lineStart = "<?php defined('COREPATH') or exit('No direct script access allowed'); ?>";
    
    public function __construct()
    {
        parent::__construct();

        $this->useDatabase();
        $this->authHelper();
        $this->always();

    }

    public function index($date = null)
    {
        $this->log($date);
    }

    public function log($date = null)
    {
        if ($date == null) {
            $date = date("Y-m-d");
        }

        $this->data['logs'] = '';
        $this->data['date'] = ($date) ? $date : date('Y-m-d');
        $file = $this->logPath . 'log-' . $date . '.php';
        
        if (!file_exists($file)) {
            return view('nolog', $this->data);
            exit;
        }

        $raw = nl2br(file_get_contents($file));
        $raw = str_replace($this->lineStart, '', $raw);
        
        $this->data['logs'] = $raw;
        return view('log', $this->data);
    }
}
