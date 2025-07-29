<?php

/**
 * @name		CodeIgniter Message Library
 * @author		Jens Segers
 * @link		http://www.jenssegers.be
 * @license		MIT License Copyright (c) 2012 Jens Segers
 * 
 */

class Messages
{
    private static $ci;

    public function __construct($params = [])
    {
        static::$ci = &get_instance();
        static::$ci->load->library('session');
        
    }

    public static function clear($type = null)
    {
        if (!empty($type)) {

            $messages = static::$ci->session->userdata('messages');

            if (!is_array($messages)) {
                $messages = [];
            }

            if (array_key_exists($type, $messages)) {
                unset($messages[$type]);
            }

            static::$ci->session->set_userdata('messages', $messages);
        } else {
            static::$ci->session->set_userdata('messages', []);
        }
    }

    public function add($message, $type = 'message')
    {
        $messages = static::$ci->session->userdata('messages');

        if (!is_array($messages)) {
            $messages = [];
        }

        if (is_a($message, 'Exception')) {
            $message = $message->getMessage();
            $type = 'error';
        }

        if (
            (!isset($messages[$type]) 
                || !in_array($message, $messages[$type])) 
                && is_string($message) && $message
        ) {
            $messages[$type][] = $message;
        }

        static::$ci->session->set_userdata('messages', $messages);
    }

    public function count($type = null)
    {
        $messages = static::$ci->session->userdata('messages');

        if (!is_array($messages)) {
            $messages = [];
        }

        if (!empty($type)) {
            if (array_key_exists($type, $messages)) {
                return count($messages[$type]);
            } else {
                return 0;
            }
        }

        $i = 0;
        foreach ($messages as $type => $m) {
            $i += count($messages[$type]);
        }
        return $i;
    }

    public static function get($type = null)
    {
        $messages = static::$ci->session->userdata('messages');

        if (!is_array($messages)) {
            $messages = [];
        }

        if (!empty($type)) {
            if (array_key_exists($type, $messages)) {
                $messages = $messages[$type];
                static::$ci->clear($type);
                return $messages;
            } else {
                return [];
            }
        }

        static::$ci->clear();
        return $messages;
    }
}
