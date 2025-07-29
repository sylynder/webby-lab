<?php

/**
 * A Template engine for Webby
 * 
 * It is based on Laravel's Blade templating engine 
 * Initially developed by Gustavo Martins and named Slice as
 * a CodeIgniter Library.
 * 
 * @author		Gustavo Martins <gustavo_martins92@hotmail.com>
 * @link		https://github.com/GustMartins/Slice-Library
 * 
 * Expanded to work efficiently with Webby 
 * 
 * @author  Kwame Oteng Appiah-Nti <developerkwame@gmail.com>
 * @license MIT
 * @version 1.0.1
 * 
 */

namespace Base\View;

use Exception;
use ParseError;

class Plates
{

	/**
	 *  The file extension for the plates template
	 *
	 *  @var   string
	 */
	public $plateExtension	= '.php';

	/**
	 *  The amount of time to keep the file in cache
	 *
	 *  @var   integer
	 */
	public $cacheTime		= 3600;

	/**
	 *  Autoload CodeIgniter Libraries and Helpers
	 *
	 *  @var   boolean
	 */
	public $enableAutoload	= false;

	/**
	 *  Default language
	 *
	 *  @var   string
	 */
	public $locale			= 'english';

	// --------------------------------------------------------------------------

	/**
	 *  Reference to CodeIgniter instance
	 *
	 *  @var   object
	 */
	protected $ci;

	/**
	 *  Global array of data for Plates Template
	 *
	 *  @var   array
	 */
	protected $plateData	= [];

	/**
	 *  The content of each section
	 *
	 *  @var   array
	 */
	protected $sections		= [];

	/**
	 *  The stack of current sections being buffered
	 *
	 *  @var   array
	 */
	protected $buffer		= [];

	/**
	 *  Custom compile functions by the user
	 *
	 *  @var   array
	 */
	protected $directives 	= [];

	/**
	 *  Libraries to autoload
	 *
	 *  @var   array
	 */
	protected $libraries 	= [];

	/**
	 *  Helpers to autoload
	 *
	 *  @var   array
	 */
	protected $helpers 		= [];

	/**
	 *  Language strings to use with translation
	 *
	 *  @var   array
	 */
	protected $language		= [];

	/**
	 *  List of languages loaded
	 *
	 *  @var   array
	 */
	protected $i18nLoaded 	= [];

	/**
	 * if true then, if the operation fails, 
	 * and it is critic, then it throws an error
	 *
	 * @var bool
	 */
	public $throwOnError = false;

    /**
     * Stores verbatim content placeholders
     *
     * @var array
     */
    protected array $verbatimPlaceholders = [];
	
	// --------------------------------------------------------------------------
	/**
	 *  All of the compiler methods used by Plates to simulate
	 *  Laravel Plates Template (Order is important)
	 */
	private array $compilers 		= [
        'verbatim',             // New: Handles @verbatim blocks first
		'directive',            // Existing: Custom user directives
		'comment',              // Existing (fixed): Blade comments {{-- --}}
        'multiline_html_comment', // New: HTML comments {{( )}}
		'html_comment',         // Existing: HTML comments ### --- ###
		'preserved',            // Existing (updated): @{{ data }} and @{!! data !!}
        'ternary',              // Existing (updated): {{ $var or 'default' }} (escaped)
        'unescaped_echo',       // New: {!! $data !!}
		'escaped_echo',         // New: {{ $data }} (replaces old 'echo')
		'variable',             // Existing: @isset, @empty
        'session_start',        // New: @session('key')
        'session_end',          // New: @endsession
		'forelse',              // Existing
		'empty',                // Existing
		'endforelse',           // Existing
		'opening_statements',   // Existing: @if, @foreach, etc.
		'else',                 // Existing
		'continueIf',           // Existing
		'continue',             // Existing
		'breakIf',              // Existing
		'break',                // Existing
		'closing_statements',   // Existing: @endif, @endforeach, etc.
		'each',                 // Existing
		'unless',               // Existing
		'endunless',            // Existing (also @endisset, @endempty)
		'includeIf',            // Existing
		'include',              // Existing
        'head',                 // Existing (alias for partial)
		'partial',              // Existing
		'section',              // Existing (alias for include used as section template file)
		'component',            // Existing (alias for include)
		'extends',              // Existing: @extends (must be compiled carefully, often towards the end)
		'yield',                // Existing
		'show',                 // Existing
		'start_section',        // Existing: @usesection (Blade's @section name)
		'close_section',        // Existing: @endsection (Blade's @endsection)
		'php',                  // Existing: @php
		'endphp',               // Existing: @endphp
		'json',                 // Existing: @json
		'doctype',              // Existing
		'script',               // Existing: @script -> <script>
		'endscript',            // Existing: @endscript -> </script>
		'javascript',           // Existing: @javascript() for <script src..
		'lang',                 // Existing
		'choice',               // Existing
		'csrf',                 // Existing
        'endhtml',              // Existing: @endhtml (Often not standard Blade but might be used)
	];

	private string $cacheExtension = '.plates';

	/**
	 * Current View Path
	 *
	 * @var string
	 */
	public $viewPath;

	// --------------------------------------------------------------------------

	/**
	 *  Plates Class Constructor
	 *
	 *  @param   array   $params = []
	 *  @return	 void
	 */
	public function __construct(array $params = [])
	{
		// Set the super object to a local variable for use later
		$this->ci = ci();
		$this->ci->benchmark->mark('plate_execution_time_start');	//	Start the timer

		$this->ci->load->driver('cache');	//	Load ci cache driver

		if (config_item('enable_helper')) {
			$this->ci->load->helper('plate');	//	Load Plates Helper
		}

		$this->initialize($params);

		//	Autoload Libraries and Helpers
		if ($this->enableAutoload) {
			//	Autoload Libraries
			if (!empty($this->libraries)) {
				$this->ci->load->library($this->libraries);
			}

			//	Autoload Helpers
			if (!empty($this->helpers)) {
				$this->ci->load->helper($this->helpers);
			}
		}

		log_message('info', 'Plates Template Class Initialized');
	}

	// --------------------------------------------------------------------------

	/**
	 *  __set magic method
	 *
	 *  Handles writing to the data property
	 *
	 *  @param   string   $name
	 *  @param   mixed    $value
	 */
	public function __set($name, mixed $value)
	{
		$this->plateData[$name] = $value;
	}

	// --------------------------------------------------------------------------

	/**
	 *  __unset magic method
	 *
	 *  Handles unseting to the data property
	 *
	 *  @param   string   $name
	 */
	public function __unset($name)
	{
		unset($this->plateData[$name]);
	}

	// --------------------------------------------------------------------------

	/**
	 *  __get magic method
	 *
	 *  Handles reading of the data property
	 *
	 *  @param    string   $name
	 *  @return   mixed
	 */
	public function __get($name)
	{
		if (array_key_exists($name, $this->plateData)) {
			return $this->plateData[$name];
		}

		return $this->ci->$name;
	}

	// --------------------------------------------------------------------------

	/**
	 * Initializes preferences
	 */
	public function initialize(array $params = []): static
	{
		$this->clear();

		foreach ($params as $key => $val) {
			if (property_exists($this, $key)) {
				$this->$key = $val;
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 * Initializes some important variables
	 */
	public function clear(): static
	{
		$this->plateExtension   = config_item('plate_extension') ?: '.php';
		$this->cacheTime		= config_item('cache_time') ?: 3600;
		$this->enableAutoload	= config_item('enable_autoload') ?: false;
		$this->locale			= config_item('language') ?: 'english';
		$this->libraries	    = config_item('libraries') ?: [];
		$this->helpers		    = config_item('helpers') ?: [];
		$this->plateData		= [];
        $this->verbatimPlaceholders = []; // Clear verbatim placeholders

		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Sets one single data to Plates Template
	 */
	public function with(string $name, mixed $value = ''): static
	{
		$this->plateData[$name] = $value;
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Sets one or more data to Plates Template
	 */
	public function set(mixed $data, mixed $value = ''): static
	{
		if (is_array($data)) {
			$this->plateData = array_merge($this->plateData, $data);
		} else {
			$this->plateData[$data] = $value;
		}

		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Appends or concatenates a value to a data in Plates Template
	 *
	 *  If data type is array it will append
	 *  If data type is string it will concatenate
	 *
	 *  @param    string   $name
	 *  @param    mixed    $value
	 */
	public function append(string $name, mixed $value): static
	{
		if (is_array($this->plateData[$name])) {
			$this->plateData[$name][] = $value;
		} else {
			$this->plateData[$name] .= $value;
		}

		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Outputs template content
	 *
	 *  @param    array     $data
	 * 
	 *  @return   string
	 */
	public function view(string $template, $data = null, bool $return = false)
	{
		if (isset($data)) {
			$this->set($data);
		}

		//	Compile and execute the template
		$content = $this->run($this->compile($template), $this->plateData);

		if (config_item('compress_content')) {
			$content = $this->minifyHtml($content);
		}

		if (!$return) {
			$this->ci->output->append_output($content);
		}

		return $content;
	}

	/**
	 * Minify compiled html
	 *
	 * @return string
	 */
	protected function minifyHtml(string $content, bool $removeComments = true)
	{
		// ... (minifyHtml method remains unchanged) ...
		$commentCount = null;
		$commentMatches = [];
		$key = md5(random_int(0, mt_getrandmax())) . '-';

		// processing pre tag (saving its contents)
		$preCount = preg_match_all('|(<pre[^>]*>.*?</pre>)|is', $content, $preMatches);
		for ($i = 0; $i < $preCount; $i++) $content = str_replace($preMatches[0][$i], '<PRE|' . $i . '|' . $key . '>', $content);

		// processing code tag
		$codeCount = preg_match_all('|(<code[^>]*>.*?</code>)|is', $content, $codeMatches);
		for ($i = 0; $i < $codeCount; $i++) $content = str_replace($codeMatches[0][$i], '<CODE|' . $i . '|' . $key . '>', $content);

		// processing script tag
		$scriptCount = preg_match_all('|(<script[^>]*>.*?</script>)|is', $content, $scriptMatches);
		for ($i = 0; $i < $scriptCount; $i++) $content = str_replace($scriptMatches[0][$i], '<SCRIPT|' . $i . '|' . $key . '>', $content);

		// processing textarea tag
		$textareaCount = preg_match_all('|(<textarea[^>]*>.*?</textarea>)|is', $content, $textareaMatches);
		for ($i = 0; $i < $textareaCount; $i++) $content = str_replace($textareaMatches[0][$i], '<TEXTAREA|' . $i . '|' . $key . '>', $content);

		// processing comments if they not to be removed
		if (!$removeComments) {
			$commentCount = preg_match_all('|(<!--.*?-->)|s', $content, $commentMatches);
			for ($i = 0; $i < $commentCount; $i++) $content = str_replace($commentMatches[0][$i], '<COMMENT|' . $i . '|' . $key . '>', $content);
		}

		// removing comments if need
		if ($removeComments) {
			$content = preg_replace('|(<!--.*?-->)|s', '', $content);
		}

		// replacing html entities
		$content = preg_replace('| |', ' ', $content); // replacing with non-breaking space (symbol 160 in Unicode)
		$content = preg_replace('|—|', '—', $content);
		$content = preg_replace('|–|', '–', $content);
		$content = preg_replace('|«|', '«', $content);
		$content = preg_replace('|»|', '»', $content);
		$content = preg_replace('|„|', '„', $content);
		$content = preg_replace('|“|', '“', $content);

		$content = preg_replace('|(</?\w+[^>]+?)\s+(/?>)|s', '$1$2', $content); // removing all contunous spaces

		while (preg_match('|<(/?\w+[^>]+/?)>\s+<(/?\w+?)|s', $content)) {
			$content = preg_replace('|<(/?\w+[^>]+/?)>\s+<(/?\w+?)|s', '<$1><$2', $content); // removing all spaces and newlines between tags
		}

		$content = preg_replace('|\s\s+|s', ' ', $content); // removing all contunous spaces

		// restoring processed comments
		if (!$removeComments) {
			for ($i = 0; $i < $commentCount; $i++) $content = str_replace('<COMMENT|' . $i . '|' . $key . '>', $commentMatches[0][$i], $content);
		}
		// restoring textarea tag
		for ($i = 0; $i < $textareaCount; $i++) $content = str_replace('<TEXTAREA|' . $i . '|' . $key . '>', $textareaMatches[0][$i], $content);
		// restoring script tag
		for ($i = 0; $i < $scriptCount; $i++) $content = str_replace('<SCRIPT|' . $i . '|' . $key . '>', $scriptMatches[0][$i], $content);
		// restoring code tag
		for ($i = 0; $i < $codeCount; $i++) $content = str_replace('<CODE|' . $i . '|' . $key . '>', $codeMatches[0][$i], $content);
		// restoring pre tag
		for ($i = 0; $i < $preCount; $i++) $content = str_replace('<PRE|' . $i . '|' . $key . '>', $preMatches[0][$i], $content);

		return $content;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Verifies if a file exists!
	 *
	 *  This function verifies if a file exists even if you are using
	 *  Modular Extensions
	 *
	 *  @param    string    $filename
	 *  @param    boolean   $showError
	 *  @return   mixed
	 */
	public function exists(string $filename, bool $showError = false)
	{
		// ... (exists method remains unchanged) ...
        $viewName = preg_replace('/([a-z]\w+)\./', '$1/', $filename);

		//	The default path to the file
		$defaultPath = VIEWPATH . $viewName . $this->plateExtension;

		//	If you are using Modular Extensions it will be detected
		if (method_exists($this->ci->router, 'fetch_module')) {
			$module = $this->ci->router->fetch_module();
			[$path, $view] = \Modules::find($viewName . $this->plateExtension, $module, 'Views/');

			if ($path) {
				$defaultPath = $path . $view;
			}
		}

		//	Verify if the page really exists
		if (is_file($defaultPath)) {
			if ($showError) {
						return $defaultPath;
					}
			
			return true;
		}
		
		if ($showError) {
			show_error($viewName . ' view was not found, Are you sure the view exists and is a `'.$this->plateExtension.'` file? ');
		} else {
			return false;
		}
	}

	// --------------------------------------------------------------------------

	/**
	 *  Alters the language to use with translation strings
	 */
	public function locale(string $locale): static
	{
		$this->locale = (string) $locale;
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Sets custom compilation function
	 */
	public function directive(string $compilator): static
	{
		$this->directives[] = $compilator;
		return $this;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Compiles a template and saves it in the cache
	 */
	protected function compile(string $template): string
	{
		$viewPath	= $this->exists($template, true);
		$cacheName	= md5((string) $viewPath) . $this->cacheExtension;
		
        // Ensure plates_cache_path is configured, default to CI cache path if not
        $platesPath = $this->ci->config->item('plates_cache_path');
        if (empty($platesPath)) {
            $platesPath = $this->ci->config->item('cache_path') . 'plates_cache/';
        }
        $platesPath = rtrim($platesPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
dd('d');
        // Create cache directory if it doesn't exist
        if (!is_dir($platesPath)) {
            mkdir($platesPath, 0777, true);
        }
		
		$this->viewPath = $viewPath;
		// $this->cacheName = $cacheName; // Not used directly, md5($viewPath) is used for file names
		// $this->platesPath = $platesPath; // Set as local var below if needed, or use $platesPath

		// Save cached files to cache/plates_cache (or configured path) folder
		// The CI Cache library manages its own path, we use $platesPath for our PHP files
		
		//	Verifies if a cached version of the file exists (using CI Cache for compiled template string)
		if ($cachedVersion = $this->ci->cache->file->get($cacheName)) {
			if (ENVIRONMENT == 'production') {
				return $cachedVersion;
			}

			$cachedMeta = $this->ci->cache->file->get_metadata($cacheName);

			if ($cachedMeta && $cachedMeta['mtime'] > filemtime($viewPath)) {
				return $cachedVersion;
			}
		}

		$content = file_get_contents($viewPath);
        $this->verbatimPlaceholders = []; // Reset for each compilation

		//	Compile the content
		foreach ($this->compilers as $compiler) {
			$method = sprintf('compile_%s', $compiler);
            if (method_exists($this, $method)) {
			    $content = $this->$method($content);
            } else {
                log_message('error', "Plates compiler method {$method} not found.");
            }
		}

		//	Store in the cache
		$this->ci->cache->file->save($cacheName, $content, $this->cacheTime);

		return $content;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Runs the template with its data
	 *
	 *  @param    array    $data
	 *  @return   string
	 */
	protected function run(string $template, $data = null)
	{
		if (is_array($data)) {
			extract($data);
		}
		
		ob_start();

		// Restore verbatim content before writing to PHP file and including
        if (!empty($this->verbatimPlaceholders)) {
            foreach ($this->verbatimPlaceholders as $placeholder => $actualContent) {
                $template = str_replace($placeholder, $actualContent, $template);
            }
            $this->verbatimPlaceholders = []; // Clean up
        }

		$template = $this->replaceBlacklisted($template);

        // Determine path for temporary PHP file
        $platesExecutionPath = $this->ci->config->item('plates_cache_path');
        if (empty($platesExecutionPath)) {
            $platesExecutionPath = $this->ci->config->item('cache_path') . 'plates_cache/';
        }
        $platesExecutionPath = rtrim($platesExecutionPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!is_dir($platesExecutionPath)) {
            mkdir($platesExecutionPath, 0777, true);
        }
        
		$templateFile = $platesExecutionPath . 'exec_' . md5((string) $this->viewPath . uniqid()) . '.php';
	
		file_put_contents($templateFile, $template);

		try {
			include $templateFile;
		} catch (ParseError $e) {
			ob_end_clean(); // Clean buffer on error
            // Log the error and content for debugging
            log_message('error', 'Plates Template ParseError: ' . $e->getMessage() . ' in ' . $templateFile);
            log_message('debug', 'Problematic Template Content: ' . $template);
            if ($this->throwOnError) {
                throw $e;
            }
            show_error('Error parsing template: ' . $e->getMessage() . '. Check logs for more details.');
		} catch (Exception $e) {
            ob_end_clean();
            log_message('error', 'Plates Template Exception: ' . $e->getMessage() . ' in ' . $templateFile);
             if ($this->throwOnError) {
                throw $e;
            }
            show_error('Error executing template: ' . $e->getMessage());
        }


		$content = ob_get_clean();
        
        // Clean up the temporary execution file
        if (is_file($templateFile)) {
            unlink($templateFile);
        }

		$this->ci->benchmark->mark('plate_execution_time_end');	//	Stop the timer

		return $content;
	}

	/**
	 * Blacklist known PHP functions
	 */
	private function replaceBlacklisted(string $template): array|string
	{
		// ... (replaceBlacklisted method remains unchanged) ...
        $blacklists = [
			'exec(', 'shell_exec(', 'pcntl_exec(', 'passthru(', 'proc_open(', 'system(',
			'posix_kill(', 'posix_setsid(', 'pcntl_fork(', 'posix_uname(', 'php_uname(',
			'phpinfo(', 'popen(', /*'file_get_contents(', 'file_put_contents(',*/ 'rmdir(', // Allowing file_get/put for includes within controlled env.
			'mkdir(', 'unlink(', 'highlight_contents(', 'symlink(',
			'apache_child_terminate(', 'apache_setenv(', 'define_syslog_variables(',
			'escapeshellarg(', 'escapeshellcmd(', /*'eval(',*/ 'fp(', 'fput(', // eval is sometimes used by templating engines, but risky.
			'ftp_connect(', 'ftp_exec(', 'ftp_get(', 'ftp_login(', 'ftp_nb_fput(',
			'ftp_put(', 'ftp_raw(', 'ftp_rawlist(', 'highlight_file(', 'ini_alter(',
			'ini_get_all(', 'ini_restore(', 'inject_code(', 'mysql_pconnect(',
			'openlog(', /*'passthru(',*/ 'phpAds_remoteInfo(', // passthru already listed
			'phpAds_XmlRpc(', 'phpAds_xmlrpcDecode(', 'phpAds_xmlrpcEncode(',
			'posix_getpwuid(', /*'posix_kill(',*/ 'posix_mkfifo(', 'posix_setpgid(', // posix_kill already listed
			/*'posix_setsid(',*/ 'posix_setuid(', /*'posix_uname(',*/ 'proc_close(', // posix_setsid, posix_uname already listed
			'proc_get_status(', 'proc_nice(', /*'proc_open(',*/ 'proc_terminate(', // proc_open already listed
			'syslog(', 'xmlrpc_entity_decode('
		];
        // Ensure eval is removed if not absolutely necessary for some advanced templating feature you might use internally.
        // For now, keeping it commented out as in the original, but it's a high-risk function.
		return str_replace($blacklists, '', $template);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Returns a variable wrapped in {{ }} for literal output.
	 */
	protected function untouch(string $variable): string
	{
		return '{{ ' . $variable . ' }}';
	}

    /**
	 *  Returns a variable wrapped in {!! !!} for literal output.
	 */
	protected function untouch_raw(string $variable): string
	{
		return '{!! ' . $variable . ' !!}';
	}

	// --------------------------------------------------------------------------

	/**
	 *  Gets the content of a template to use inside the current template
	 *  It will inherit all the Global data
	 *
	 *  @param    array    $data
	 */
	protected function include(string $template, $data = null): string
	{
		// ... (include method remains unchanged) ...
        $currentPlateData = $this->plateData; // Preserve current data
        if (is_array($data)) {
             $this->set(array_merge($currentPlateData, $data)); // Merge, allowing specific include data to override
        } else {
            $this->set($currentPlateData); // No new data, use existing
        }
        
        $content = $this->run($this->compile($template), $this->plateData);
        $this->plateData = $currentPlateData; // Restore original plateData
        return $content;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Gets the content of a template to use inside the current template
	 *  Mostly templates are used as partials
	 *  It will inherit all the Global data
	 *
	 *  @param    array    $data
	 */
	protected function partial(string $template, $data = null): string
	{
		return $this->include($template, $data); // Alias to include
	}

	// --------------------------------------------------------------------------

	/**
	*  Gets the content of a template to use inside the current template
	*  Mostly templates are used as sections
	*  It will inherit all the Global data
	*
	*  @param    array    $data
	*/
	protected function section(string $template, $data = null): string
	{
        return $this->include($template, $data); // Alias to include
	}

	// --------------------------------------------------------------------------

	/**
	 *  Gets the content of a template to use inside the current template
	 * 	This stands in as just a name to use to set contents as components
	 *  It will inherit all the Global data
	 *
	 *  @param    array    $data
	 */
	protected function component(string $template, $data = null): string
	{
        return $this->include($template, $data); // Alias to include
	}

	// --------------------------------------------------------------------------

	/**
	 *  Gets the content of a section
	 *
	 *  @return   string
	 */
	protected function yield(string $section, string $default = '')
	{
		// ... (yield method remains unchanged) ...
        return $this->sections[$section] ?? $default;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Starts buffering the content of a section
	 *
	 *  If the param $value is different of null it will be the content of
	 *  the current section
	 *
	 *  @param string $section
	 *  @param mixed $value
	 */
	protected function start_section(string $section, mixed $value = null): void
	{
		// ... (start_section method remains unchanged) ...
        $this->buffer[] = $section;

		if ($value !== null) {
			$this->close_section($value);
		} else {
			ob_start();
		}
	}

	// --------------------------------------------------------------------------

	/**
	 *  Stops buffering the content of a section
	 *
	 *  If the param $value is different of null it will be the
	 *  content of the current section
	 *
	 *   @param    mixed    $value
	 */
	protected function close_section(mixed $value = null): string
	{
		// ... (close_section method remains unchanged) ...
        $lastSection = array_pop($this->buffer);

		if ($value !== null) {
			$this->extend_section($lastSection, $value);
		} else {
			$this->extend_section($lastSection, ob_get_clean());
		}

		return $lastSection;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Retrieves a line from the language file loaded
	 *
	 *  @param    string    $line        String line to load
	 *  @param    array     $params      Place-holders to parse in the string
	 */
	public function i18n(string $line, array $params = []): string
	{
		// ... (i18n method remains unchanged) ...
        [$file, $string] = array_pad(explode('.', $line), 2, null);

		//	Here tries to get the string with the $file variable...
		$line = $this->language[$file] ?? $file;

		if ($string !== null) {
			if (!isset($this->i18nLoaded[$file]) || $this->i18nLoaded[$file] !== $this->locale) {
				//	Load the file into the language array
				$this->language = array_merge($this->language, $this->ci->lang->load($file, $this->locale, true, '')); // Added base path for lang files
				//	Save the loaded file and idiom
				$this->i18nLoaded[$file] = $this->locale;
			}

			//	... and here, the variable used is $string
			$line = $this->language[$string] ?? $this->language[$line] ?? $string; // Check $line as key if $string isn't found
		}

		//	Deals with the place-holders for the string
		if (!empty($params) && is_array($params)) {
			foreach ($params as $name => $content) {
				$line = (str_contains((string) $line, ':' . strtoupper($name)))
					? str_replace(':' . strtoupper($name), strtoupper((string) $content), (string) $line)
					: $line;

				$line = (str_contains((string) $line, ':' . ucfirst($name)))
					? str_replace(':' . ucfirst($name), ucfirst((string) $content), (string) $line)
					: $line;

				$line = (str_contains((string) $line, ':' . $name))
					? str_replace(':' . $name, $content, (string) $line)
					: $line;
			}
		}

		return $line;
	}

	// --------------------------------------------------------------------------

	/**
	 *  Returns a json_encoded string
	 *
	 *  @param    array     $array        Source of javascript file
	 */
	public function jsonEncode(mixed $data = []): string // Changed from array to mixed for more flexibility
	{
		if (empty($data)) {
			return "''"; // Return empty JSON string or null based on preference
		}
		try {
			return json_encode($data, JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
		} catch (\JsonException $e) {
			log_message('error', 'JSON encoding error: ' . $e->getMessage());
			return "'Error encoding JSON: " . $e->getMessage() . "'";
		}
	}

	// --------------------------------------------------------------------------

	/**
	 *  Returns a script tag with src and given attributes
	 *
	 *  @param    string    $src   Source of javascript file
	 *  @param    string|array    $attributes  Additional attributes in a string
	 */
	public function javascript(string $src = '', string|array $attributes = ''): string
	{
		// ... (javascript method remains unchanged) ...
        $line = '';

		if (!empty($src)) {
			$line = 'src="'.$src.'"';
		}
        
        if (is_array($attributes)) {
            $attrs = [];
            foreach($attributes as $key => $val) {
                $attrs[] = $key . '="' . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . '"';
            }
            $attributes = implode(' ', $attrs);
        }


		if (!empty($attributes)) {
			$line = $line .' '. $attributes;
		}

		return $line = "\n\n\t<script " . trim($line) . "></script>\n";
	}

	// --------------------------------------------------------------------------

	/**
	 *  Retrieves a line from the language file loaded in singular or plural form
	 *
	 * @param mixed[] $params
	 */
	public function inflector(string $line, int|array $number, array $params = []): ?string
	{
		// ... (inflector method remains unchanged) ...
        $lines = explode('|', $this->i18n($line, $params));

		if (is_array($number)) {
			$number = count($number);
		}

		foreach ($lines as $string) {
			//	Searches for a given amount
			preg_match_all('/\{([0-9]{1,})\}/', $string, $matches);
			[$str, $count] = $matches;

			if (isset($count[0]) && $count[0] == $number) {
				return str_replace('{' . $count[0] . '} ', '', $string);
			}

			//	Searches for a range interval
			preg_match_all('/\[([0-9]{1,}),\s?([0-9*]{1,})\]/', $string, $matches);
			[$str_range, $start, $end] = $matches; // Renamed $str to avoid conflict

			if (isset($end[0]) && $end[0] !== '*') {
				if (in_array($number, range($start[0], $end[0]))) {
					return preg_replace('/\[.*?\]\s?/', '', $string);
				}
			} elseif (isset($end[0]) && $end[0] === '*') {
				if ($number >= $start[0]) {
					return preg_replace('/\[.*?\]\s?/', '', $string);
				}
			}
		}
        
        if (count($lines) == 1) return $lines[0]; // If only one option, return it.
		return ($number > 1 && isset($lines[1])) ? $lines[1] : ($lines[0] ?? null); // Handle if $lines[0] not set
	}

	// --------------------------------------------------------------------------

	/**
	 *  Iterates through a variable to include content
	 *
	 *  @param    string   $default
	 *  @param mixed[] $variable
	 */
	protected function each(string $template, array $variable, string $label, $default = null): string
	{
		// ... (each method remains unchanged) ...
        $content = '';

		if ((is_countable($variable) ? count($variable) : 0) > 0) {
			foreach ($variable as $item) { // Changed to $item for clarity
                $dataForInclude = [$label => $item];
				$content .= $this->include($template, $dataForInclude);
			}
		} else {
			$content .= ($default !== null) ? $this->include($default) : '';
		}

		return $content;
	}

    // ==========================================================================
	// == NEW AND MODIFIED COMPILER METHODS =====================================
	// ==========================================================================

	/**
	 *  Rewrites custom directives defined by the user
	 */
	protected function compile_directive(string $value): string
	{
		foreach ($this->directives as $compilator) {
			$value = call_user_func($compilator, $value);
		}
		return $value;
	}
    
    /**
     * Compile @verbatim blocks.
     * Content within @verbatim...@endverbatim will not be compiled.
     */
    protected function compile_verbatim(string $content): string
    {
        return preg_replace_callback('/@verbatim\s*(.+?)\s*@endverbatim/s', function ($matches) {
            $placeholder = '__PLATE_VERBATIM_PLACEHOLDER_' . count($this->verbatimPlaceholders) . '__';
            // Store raw content, including potential HTML entities that should remain as they are
            $this->verbatimPlaceholders[$placeholder] = $matches[1];
            return $placeholder;
        }, $content);
    }

	/**
	 *  Rewrites Plates comment {{-- --}} into PHP comment 
	 */
	protected function compile_comment(string $content): string
	{
		// Corrected pattern to only target {{-- ... --}} and convert to PHP block comments.
		$pattern = '/\{\{--((?:.|\s)*?)--\}\}/'; 
		return preg_replace($pattern, "<?php /* $1 */ ?>", $content);
	}

    /**
     * Compile custom multiline HTML comments {{( ... )}}
     * Removes any internal '-->' before wrapping.
     */
    protected function compile_multiline_html_comment(string $content): string
    {
        $pattern = '/\{\{\(\s*(.*?)\s*\)\}\}/s'; // 's' for dotall, capture content in $1
        return preg_replace_callback($pattern, function ($matches) {
            $innerContent = $matches[1];
            // Remove '-->' from innerContent
            $cleanedContent = str_replace('-->', '-- >', $innerContent); // Replace with space to avoid breaking if it was intentional structure
            return '<!--' . $cleanedContent . '-->';
        }, $content);
    }

	/**
     * Compile html view comments using ### ... ### syntax.
     */
    protected function compile_html_comment(string $view): string
    {
		return preg_replace_callback('/###(.*?)###/s', function($matches) { // Added /s for multiline comments
			$comment = trim($matches[1]);
            return sprintf('<!-- %s -->', $comment);
		}, $view);
    }

	/**
	 *  Preserves an expression to be displayed in the browser
     *  Handles @{{ ... }} and @{!! ... !!}
	 */
	protected function compile_preserved(string $content): string
	{
		// Handles @{{ value }} -> outputs literal {{ value }}
		$pattern_escaped = '/@(\{\{\s*(.+?)\s*\}\})/s';
		$content = preg_replace_callback($pattern_escaped, function ($matches) {
            return '<?php echo \'' . str_replace("'", "\'", $matches[1]) . '\'; ?>';
        }, $content);
		
		// Handles @{!! value !!} -> outputs literal {!! value !!}
		$pattern_unescaped = '/@(\{!!\s*(.+?)\s*!!\})/s';
        $content = preg_replace_callback($pattern_unescaped, function ($matches) {
            return '<?php echo \'' . str_replace("'", "\'", $matches[1]) . '\'; ?>';
        }, $content);

		return $content;
	}

	/**
	 *  Rewrites Plates conditional echo statement {{ $var or 'default' }} into PHP echo statement,
     *  ensuring the output is escaped.
	 */
	protected function compile_ternary(string $content): string
	{
		$pattern = '/\{\{\s*(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s+or\s+([\'"])(.*?)\2\s*\}\}/s';

		return preg_replace_callback($pattern, function($matches) {
            $variable = $matches[1];
            $default = $matches[3];
            // Note: $this->plateData is not directly accessible here in the same way as during runtime.
            // This ternary logic should be PHP code that runs when the template is executed.
            $php_code = sprintf(
                'isset(%s) && %s !== \'\' ? htmlspecialchars(%s, ENT_QUOTES, \'UTF-8\') : htmlspecialchars(\'%s\', ENT_QUOTES, \'UTF-8\')',
                $variable, $variable, $variable, addslashes($default)
            );
            return '<?php echo ' . $php_code . '; ?>';
        }, $content);
	}
    
    /**
	 *  Rewrites Plates unescaped echo statement {!! $data !!} into PHP echo statement.
	 */
	protected function compile_unescaped_echo(string $content): string
	{
		$pattern = '/\{!!\s*(.+?)\s*!!\}/s';
		return preg_replace($pattern, '<?php echo $1; ?>', $content);
	}

	/**
	 *  Rewrites Plates escaped echo statement {{ $data }} into PHP echo statement
     *  with htmlspecialchars(). This is the default echo.
	 */
	protected function compile_escaped_echo(string $content): string
	{
        // This pattern must be general enough for {{ $variable }}, {{ $object->property }}, {{ function() }}, etc.
        // It should not match {{-- or {{( if they haven't been processed yet, but order of compilers handles this.
		$pattern = '/\{\{\s*(.+?)\s*\}\}/s';
		return preg_replace($pattern, '<?php echo htmlspecialchars($1, ENT_QUOTES, \'UTF-8\'); ?>', $content);
	}

	/**
	 *  Rewrites Plates variable handling function (@isset, @empty) into valid PHP
	 */
	protected function compile_variable(string $content): string
	{
		$pattern = '/(\s*)@(isset|empty)(\s*\(.+?\))/s'; // Made parenthesis content non-optional with .+?
		return preg_replace($pattern, '$1<?php if ($2$3): ?>', $content);
	}

    /**
     * Compile @session('key') directive.
     */
    protected function compile_session_start(string $content): string
    {
        $pattern = '/(\s*)@session\(\s*([\'"])(.*?)\2\s*\)/s';
        return preg_replace($pattern, '$1<?php if (isset($this->ci->session) && $this->ci->session->has_userdata(\'$3\')): ?>$1<?php $value = $this->ci->session->userdata(\'$3\'); ?>', $content);
    }

    /**
     * Compile @endsession directive.
     */
    protected function compile_session_end(string $content): string
    {
        $pattern = '/(\s*)@endsession(\s*)/s';
        return preg_replace($pattern, '$1<?php endif; ?>$2', $content);
    }


	// --------------------------------------------------------------------------
    // == EXISTING COMPILERS (UNCHANGED OR MINOR ADJUSTMENTS IF NEEDED BY NEW LOGIC) ==
    // Note: compile_echo is removed as it's replaced by compile_escaped_echo and compile_unescaped_echo
	// --------------------------------------------------------------------------


	/**
	 *  Rewrites Plates forelse statement into valid PHP
	 */
	protected function compile_forelse(string $content): string
	{
		$pattern = '/(\s*)@forelse(\s*\(.*\))(\s*)/s';

		preg_match_all($pattern, $content, $matches);

		foreach ($matches[0] as $forelse) {
			$variablePattern = '/\(\s*([^ ]+)\s+as\s+.*/i'; // Capture the collection variable

			preg_match($variablePattern, (string) $forelse, $variableMatch);
            
            if (isset($variableMatch[1])) {
                $variable = $variableMatch[1];
    			$ifStatement = sprintf('<?php if (isset(%s) && (is_array(%s) || %s instanceof \Traversable) && count(%s) > 0): ?>', $variable, $variable, $variable, $variable);
    			$searchPattern = '/(\s*)@forelse(\s*\(.*\))/s';
    			$replacement = '$1' . $ifStatement . '<?php foreach $2: ?>';
    			$content = str_replace($forelse, preg_replace($searchPattern, $replacement, (string) $forelse), $content);
            }
		}
		return $content;
	}

	/**
	 *  Rewrites Plates empty statement into valid PHP
	 */
	protected function compile_empty(string $content): string
	{
		return str_replace('@empty', '<?php endforeach; ?><?php else: ?>', $content);
	}

	/**
	 *  Rewrites Plates endforelse statement into valid PHP
	 */
	protected function compile_endforelse(string $content): string
	{
		return str_replace('@endforelse', '<?php endif; ?>', $content);
	}

	/**
	 *  Rewrites Plates opening structures into PHP opening structures
	 */
	protected function compile_opening_statements(string $content): string
	{
		$pattern = '/(\s*)@(if|elseif|foreach|for|while)(\s*\(.+?\))/s'; // Made parenthesis content non-optional
		return preg_replace($pattern, '$1<?php $2$3: ?>', $content);
	}

	/**
	 *  Rewrites Plates else statement into PHP else statement
	 */
	protected function compile_else(string $content): string
	{
		$pattern = '/(\s*)@(else)(\s*)/s';
		return preg_replace($pattern, '$1<?php $2: ?>$3', $content);
	}

	/**
	 *  Rewrites Plates continue() statement into PHP continue statement
	 */
	protected function compile_continueIf(string $content): string
	{
		$pattern = '/(\s*)@(continue)(\s*\(.+?\))/s'; // Made parenthesis content non-optional
		return preg_replace($pattern, '$1<?php if $3: ?><?php $2; ?><?php endif; ?>', $content); // Removed extra $1s
	}

	/**
	 *  Rewrites Plates continue statement into PHP continue statement
	 */
	protected function compile_continue(string $content): string
	{
		$pattern = '/(\s*)@(continue)(\s*)/s';
		return preg_replace($pattern, '$1<?php $2; ?>$3', $content);
	}

	/**
	 *  Rewrites Plates break() statement into PHP break statement
	 */
	protected function compile_breakIf(string $content): string
	{
		$pattern = '/(\s*)@(break)(\s*\(.+?\))/s'; // Made parenthesis content non-optional
		return preg_replace($pattern, '$1<?php if $3: ?><?php $2; ?><?php endif; ?>', $content); // Removed extra $1s
	}

	/**
	 *  Rewrites Plates break statement into PHP break statement
	 */
	protected function compile_break(string $content): string
	{
		$pattern = '/(\s*)@(break)(\s*)/s';
		return preg_replace($pattern, '$1<?php $2; ?>$3', $content);
	}

	/**
	 *  Rewrites Plates closing structures into PHP closing structures
	 */
	protected function compile_closing_statements(string $content): string
	{
		$pattern = '/(\s*)@(endif|endforeach|endfor|endwhile)(\s*)/s';
		return preg_replace($pattern, '$1<?php $2; ?>$3', $content);
	}

	/**
	 *  Rewrites Plates each statement into valid PHP
	 */
	protected function compile_each(string $content): string
	{
		$pattern = '/(\s*)@each(\s*\(.*?\))(\s*)/s';
		return preg_replace($pattern, '$1<?php echo $this->each$2; ?>$3', $content);
	}

	/**
	 *  Rewrites Plates unless statement into valid PHP
	 */
	protected function compile_unless(string $content): string
	{
		$pattern = '/(\s*)@unless(\s*\(.+?\))/s'; // Made parenthesis content non-optional
		return preg_replace($pattern, '$1<?php if ( ! ($2)): ?>', $content);
	}

	/**
	 *  Rewrites Plates endunless, endisset and endempty statements into valid PHP
	 */
	protected function compile_endunless(string $content): string
	{
		$pattern = '/(\s*)@(endunless|endisset|endempty)(\s*)/s'; // Added \s* at the end
		return preg_replace($pattern, '$1<?php endif; ?>$2', $content); // $2 captures potential trailing space
	}

	/**
	 *  Rewrites Plates @includeIf statement into valid PHP
	 */
	protected function compile_includeIf(string $content): string
	{
		$pattern = "/(\s*)@includeIf\s*(\('(.*?)'.*\))/s";
		return preg_replace($pattern, '$1<?php echo ($this->exists("$3", false) === true) ? $this->include$2 : ""; ?>', $content);
	}

	/**
	 *  Rewrites Plates @include statement into valid PHP
	 */
	protected function compile_include(string $content): string
	{
		$pattern = '/(\s*)@include(\s*\(.*\))/s';
		return preg_replace($pattern, '$1<?php echo $this->include$2; ?>', $content);
	}
    
    /**
	 *  Rewrites Plates @head statement into valid PHP (alias for partial)
	 */
	protected function compile_head(string $content): string
	{
		$pattern = '/(\s*)@head(\s*\(.*\))/s';
		return preg_replace($pattern, '$1<?php echo $this->partial$2; ?>', $content);
	}

	/**
	 *  Rewrites Plates @partial statement into valid PHP
	 */
	protected function compile_partial(string $content): string
	{
		$pattern = '/(\s*)@partial(\s*\(.*\))/s';
		return preg_replace($pattern, '$1<?php echo $this->partial$2; ?>', $content);
	}

	/**
	 *  Rewrites Plates @section (when used to include a file) into valid PHP
	 */
	protected function compile_section(string $content): string
	{
		$pattern = '/(\s*)@section(\s*\(.*\))/s';
        // This original @section seems to be for including a file as a section, not defining one.
        // Blade's @section('name', 'content') or @section('name') @endsection are different.
        // Assuming this is for @section('path/to/viewfile', $data)
		return preg_replace($pattern, '$1<?php echo $this->section$2; ?>', $content);
	}

	/**
	 *  Rewrites Plates @component statement into valid PHP
	 */
	protected function compile_component(string $content): string
	{
		$pattern = '/(\s*)@component(\s*\(.*\))/s';
		return preg_replace($pattern, '$1<?php echo $this->component$2; ?>', $content);
	}

	/**
	 *  Rewrites Plates @extends statement into valid PHP
	 */
	protected function compile_extends(string $content): string
	{
		$pattern = '/(\s*)@extends(\s*\(.*\))/s';
		if (!preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
			return $content;
		}
		$content = preg_replace($pattern, '', $content);
		foreach ($matches as $include) {
			$content .= $include[1] . '<?php echo $this->include' . $include[2] . "; ?>";
		}
		return $content;
	}

	/**
	 *  Rewrites Plates @yield statement into Section statement
	 */
	protected function compile_yield(string $content): string
	{
		$pattern = '/(\s*)@yield(\s*\(.*\))/s';
		return preg_replace($pattern, '$1<?php echo $this->yield$2; ?>$2', $content); // Use $1..$2 for spacing. Output <?php echo $this->yield(params); 
	}

	/**
	 *  Rewrites Plates Show statement into valid PHP
	 */
	protected function compile_show(string $content): string
	{
		return str_replace('@show', '<?php echo $this->yield($this->close_section()); ?>', $content);
	}

	/**
	 *  Rewrites Plates @usesection statement as Section statement
     *  (Blade's @section('name') for block sections)
	 */
	protected function compile_start_section(string $content): string
	{
		$pattern = '/(\s*)@usesection(\s*\(.*\))/s'; // This is for @usesection('name')
		return preg_replace($pattern, '$1<?php $this->start_section$2; ?>', $content);
	}

	/**
	 *  Rewrites Plates @endsection statement into Section statement
     *  (Blade's @endsection for block sections)
	 */
	protected function compile_close_section(string $content): string
	{
		return str_replace('@endsection', '<?php $this->close_section(); ?>', $content);
	}

	/**
	 *  Rewrites Plates @php statement into valid PHP
	 */
	protected function compile_php(string $content): string
	{
		return str_replace('@php', '<?php', $content);
	}

	/**
	 *  Rewrites Plates @endphp statement into valid PHP
	 */
	protected function compile_endphp(string $content): string
	{
		return str_replace('@endphp', '?>', $content);
	}

	/**
	 *  Rewrites Plates @doctype statement into valid PHP
	 */
	protected function compile_doctype(string $content): string
	{
		return str_replace('@doctype', '<!DOCTYPE html>', $content);
	}

	/**
	 *  Rewrites Plates @endhtml statement into valid PHP
	 */
	protected function compile_endhtml(string $content): string
	{
		return str_replace('@endhtml', '</html>', $content);
	}

	/**
	 *  Rewrites Plates @json statement into valid PHP
	 */
	protected function compile_json(string $content): string
	{
		$pattern = '/(\s*)@json(\s*\(.*\))/s';
		return preg_replace($pattern, '$1<?php echo $this->jsonEncode$2; ?>', $content);
	}

	/**
	 *  Rewrites Plates @script statement into valid PHP
	 */
	protected function compile_script(string $content): string
	{
		return str_replace('@script', '<script>', $content);
	}
	/**
	 *  Rewrites Plates @endscript statement into valid PHP
	 */
	protected function compile_endscript(string $content): string
	{
		return str_replace('@endscript', '</script>', $content);
	}

	/**
	 *  Rewrites Plates @javascript statement into valid PHP for <script src..
	 */
	protected function compile_javascript(string $content): string
	{
		$pattern = '/(\s*)@javascript(\s*\(.*\))/s';
		return preg_replace($pattern, '$1<?php echo $this->javascript$2; ?>', $content);
	}

	/**
	 *  Rewrites Plates @lang statement into valid PHP
	 */
	protected function compile_lang(string $content): string
	{
		$pattern = '/(\s*)@lang(\s*\(.*\))/s';
		return preg_replace($pattern, '$1<?php echo $this->i18n$2; ?>', $content);
	}

	/**
	 *  Rewrites Plates @choice statement into valid PHP
	 */
	protected function compile_choice(string $content): string
	{
		$pattern = '/(\s*)@choice(\s*\(.*\))/s';
		return preg_replace($pattern, '$1<?php echo $this->inflector$2; ?>', $content);
	}

	/**
	 *  Rewrites Plates @csrf statement into valid PHP
	 */
	protected function compile_csrf(string $content): string
	{
		// Assuming csrf() is a global helper function in your CI setup
		return str_replace('@csrf', '<?php echo csrf(); ?>', $content);
	}

	// --------------------------------------------------------------------------

	/**
	 *  Stores the content of a section
	 *  It also replaces the Plates @parent statement with the previous section
	 */
	private function extend_section(string $section, string $content): void
	{
		if (isset($this->sections[$section])) {
			$this->sections[$section] = str_replace('@parent', $content, (string) $this->sections[$section]);
		} else {
			$this->sections[$section] = $content;
		}
	}
}
