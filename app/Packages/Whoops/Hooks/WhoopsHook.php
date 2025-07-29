<?php

use Whoops\Util\Misc;
use Whoops\Handler\Handler;
use Whoops\Handler\JsonResponseHandler;

class WhoopsHook 
{
    /**
     *  Evalutated File
     *
     * @var string
     */
    private $evaluatedFile = null;

    /**
     * Parse Error Constant
     */
    private const PARSE_ERROR = 4;

    /**
     * Error Zero Constant
     */
    private const ERROR_ZERO = 0;

    private function session() 
    {
        return get_instance()->session;
    }

    public function bootWhoops() 
    {
        
        $whoops = new \Whoops\Run;

        $whoops->pushHandler(function($exception, $inspector, $run) {

            if ($this->checkEvaluated($exception->getFile())) {
                
                $this->evaluatedFile = static::session()->userdata('__view_path');

                static::session()->set_userdata('__line', $exception->getLine());

                static::session()->set_userdata('__evaluated', true);
            }

            $file = isset($this->evaluatedFile) ? $this->evaluatedFile : $exception->getFile();
            $line = $exception->getLine();
            $message = $exception->getMessage();
            $num = $exception->getCode();

            if ($num == self::ERROR_ZERO) { 
                $num = self::PARSE_ERROR;
            }
            
            $_error = & load_class('Exceptions', 'core');
            $_error->log_exception($num, $message, $file, $line);

            return Handler::DONE;
        });

        // Enable JsonResponseHandler when request is AJAX
        if (Misc::isAjaxRequest()) {
                $whoops->pushHandler(new JsonResponseHandler());
        }

        $whoops->pushHandler(new WhoopsErrorOutput);
        $whoops->register();
    }

    private function checkEvaluated($file)
	{
		$evaluated = false;

		if (strpos($file , "eval()'d code") !== false) {
			$evaluated = true;
		}

		return $evaluated;
	}
}

class WhoopsErrorOutput extends \Whoops\Handler\PrettyPageHandler
{
    public function handle()
    {
        
        $this->setEditor(config_item('use_editor'));

        if (config_item('hide_sensitive_data')) {
            foreach ( $_ENV as $key => $value ) { parent::blacklist('_ENV', $key);}
            foreach ( $_SERVER as $key => $value ) { parent::blacklist('_SERVER', $key);}
        }

        if (config_item('hide_session_data')) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start(); //the good old friend
            }
            foreach ( $_SESSION as $key => $value ) { parent::blacklist('_SESSION', $key);}
        }

        if (config_item('hide_cookie_data')) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start(); //the good old friend
            }
            foreach ( $_COOKIE as $key => $value ) { parent::blacklist('_COOKIE', $key);}
        }

        if (ENVIRONMENT === 'production' || ENVIRONMENT === 'testing' ) {
            $_SERVER = [];
            $_ENV = [];
            $_SESSION = [];
            $_COOKIE = [];
        }

        parent::handle();
    }

}
