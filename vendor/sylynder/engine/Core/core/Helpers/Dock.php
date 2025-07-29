<?php

/**
 * Dock
 *
 * A namespace loader library for CodeIgniter 3
 *
 * NOTICE OF LICENSE
 *
 * Free as in free beer
 *
 * This library is free as in free beer :)
 *
 * @package     Dock
 * @author      William Knauss
 * @license     Free as in Free Beer
 * https://github.com/mpmont/Dock
 * 
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Dock {
    /**
     * CodeIgniter Super Global
     * Holds the CodeIgniter super global object
     *
     * @access protected
     * @var CI_Controller
     */
    protected $CI = null;

    /**
     * Files
     * Keep track of namespaces and filenames
     *
     * @access protected
     * @var array
     */
    protected $files = array();

    /**
     * Use Reflection
     * Should we use reflection to start an instance of the
     * specified namespace sending proper parameters to the 
     * construct. 
     *
     * @access protected
     * @var boolean
     */
    protected $use_reflection = true;

    /**
     * Class Construct
     * Setup the CodeIgniter super global object
     *
     * @access public
     */
    public function __construct() {
        //reference the CI object
        $this->CI = &get_instance();

        //set a debug message
        log_message('debug', 'Dock Class Initialized');
    }

    /**
     * Library
     * Loads a namespaced library
     *
     * @access public
     * @param  [string] $namespace
     * @param  [array] $params
     * @param  [string] $object_name
     */
    public function library($namespace, $params = NULL, $object_name = NULL) {
        $this->_load('libraries',$namespace,$params,$object_name);
    }

    /**
     * Model
     * Loads a namespaced model
     *
     * @access public
     * @param  [string] $namespace
     * @param  [array] $params
     * @param  [string] $object_name
     */
    public function model($namespace, $params = NULL, $object_name = NULL) {
        $this->_load('models',$namespace,$params,$object_name);
    }

    /**
     * File
     * Loads a namespaced file
     *
     * @access public
     * @param  [string] $path
     * @param  [string] $namespace
     * @param  [array] $params
     * @param  [string] $object_name
     */
    public function file($path, $namespace, $params = NULL, $object_name = NULL) {
        $this->_load($path,$namespace,$params,$object_name);
    }

    /**
     * Composer
     * Loads a composer package
     *
     * @access public
     * @param  [string] $namespace
     * @param  [string] $params
     * @param  [array] $object_name
     */
    public function composer($namespace, $params = NULL, $object_name = NULL) {
        $this->_load('composer',$namespace,$params,$object_name);
    }

    /**
     * Alias
     * Allows a namespaced class to be loaded under an Alias
     *
     * @access public
     * @param  [string] $namespace
     * @param  [string] $alias
     * @return [bool]
     */
    public function alias($namespace,$alias) {
        class_alias($namespace,$alias);
        return true;
    }

    /**
     * Load
     * Does the real work for this library
     *
     * @access protected
     * @param  [string] $type
     * @param  [string] $namespace
     * @param  [array] $params
     * @param  [string] $object_name
     */
    protected function _load($type, $namespace, $params = NULL, $object_name = NULL) {
        //lets explode the namespace to find our path
        $path_segments = explode('\\',$namespace);
        $library_name  = $path_segments[count($path_segments)-1];

        //if we don't have an array greater than 1 then we aren't working with a namespace
        //While this may not hold try we will deal with that at another time
        if (count($path_segments)==1) {
            show_error('X-1');
        }

        //unset the last segment since that is the class name
        unset($path_segments[count($path_segments)-1]);

        //set our path
        switch (strtolower($type)) {
            case 'models':
            case 'libraries':
                $path = APPPATH.strtolower($type);
            break;

            case 'composer':
                $path = '-1';
                $library_path = 'COMPOSER';
            break;

            default:
                $path = $type;
            break;
        }

        //only check the path if we have to
        if ($path!='-1') {
            //update our path with the namespace folders
            $path = $path.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR,$path_segments);

            //does this directory exists?
            if (!is_dir($path)) {
                show_error('X-2');
            }

            //make sure our library exists
            $library_path = $path.DIRECTORY_SEPARATOR.$library_name.'.php';
            if (!file_exists($library_path)) {
                show_error('X-3');
            }

            //we should use $this->CI->load->file($library_path);?
            //we should only do this is the class doesn't already exists
            if (!class_exists($namespace)) {
                include($library_path);
            }
        }

        //we have included the class files but lets make sure the class actually exists
        if (!class_exists($namespace)) {
            log_message('error', 'Non-existent class: '.$namespace);
            show_error('Non-existent class: '.$namespace);
        }

        //reference var
        $reference = &$this->CI;
        foreach ($path_segments as $ref) {
            //only set it if it has not been set yet
            if (!isset($reference->$ref)) {
                $reference->$ref = new stdClass();
            }

            //build our next reference var
            $reference = &$reference->$ref;
        }

        //does the dev want to rename the object?
        if (!is_null($object_name)) {
            $library_name = $object_name;
        }

        //check to see if the reference currently exists
        if (isset($reference->$library_name)) {
            //since it does exists can we find the path?
            if (isset($this->files[$namespace])) {
                //since the path doesn't match our current path the dev must have "doubled" named a library
                //lets error out
                if ($this->files[$namespace]!=$library_path) {
                    show_error('The namespace name you are loading is the name of a resource that is already being used: '.$namespace);
                }
            }
            else {
                //the dev has decided to load this twice, lets ignore this attempt
                log_message('debug', $namespace.' class already loaded. Second attempt ignored.');
                return;
            }
        }

        //should we use reflection?
        if (($this->use_reflection===true) and (class_exists('ReflectionClass'))) {
            $reflect   = new ReflectionClass($namespace);
            $construct = $reflect->getConstructor();
            
            //is there a class construct?
            if (!is_null($construct)) {
                //is params an array?
                if (!is_array($params)) {
                    //if not copy the value
                    $value    = $params;

                    //just for good times sake nullify the params value
                    $params   = null;
                    //now set params as an array and add our value
                    $params[] = $value;
                }

                $reference->$library_name = $reflect->newInstanceArgs($params);
            }
            else {
                //since there is no construct lets just boot up
                $reference->$library_name = new $namespace;
            }
        }
        else {
            //now set our class
            $reference->$library_name = new $namespace($params);
        }

        //keep track of our namespaces and file names, maybe someday we will add this to the profiler
        $this->files[$namespace] = $library_path;
    }
}

/* End of file dock.php */
/* Location: ./application/libraries/Dock.php */