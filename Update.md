### List of changes to come to Webby

### Resend API


APIKEY: re_YrcbZcuH_DnK4X5bpLjZa6w12aozBnS7G

wrk -t12 -c400 -d30s http://developerkwame.local:8085

https://www.youtube.com/@aliaslan4802/videos

PHP 3 dots in method syntax medium

Type hinting - difference between CLosure and Callable

```php

// Route::domain('*.developerkwame.local')->group(function () {});

Route::domain('learn.developerkwame.local')->group(function () {
    Route::get('domain', 'app/learn');
    Route::get('dynamic', 'app/dynamic');
});

// specific routes
Route::domain('teach.developerkwame.local')->group(function () {
    Route::get('domain', 'app/teach');
    Route::get('dynamic', 'app/dynamic');
});

// default sub domain route
Route::domain('uses.developerkwame.local')->group(function () {
    Route::get('domain', 'app/uses');
    Route::get('dynamic', 'app/dynamic');
});

// dynamic subdomain routes
// Route::domain('*.developerkwame.local')->group(function () {
//     Route::get('domain', 'app::test');
//     Route::get('dynamic', 'app/dynamic');
// });

// Route::domain('learn.developerkwame.local', 'learn/books/create')->group(function () {
//     Route::get('test-user', 'backend/dashboard/home'); 
//     Route::get('school', 'school/books'); 
// });



if (preg_match('/\{.*\}/', static::$subdomain)) {
	static::$subdomain = "*";
}

if ((static::$subdomain === '*') && ($definedController === null) && SUBDOMAIN !== false) {
	$currentDomain = static::$subdomain = Route::dynamic();
	// static::$subdomain = $currentDomain;
	dd('here star-');
}
// dd((static::$subdomain === '*') && ($definedController === null) && SUBDOMAIN !== false);
if (($currentDomain === static::$subdomain) && ($definedController !== null)) {
	static::$definedController = $definedController;
	dd('here defined-');
}

if ((static::$subdomain === '*') && ($definedController !== null)) {
	// static::$definedController = $definedController;
	$currentDomain = Route::dynamic();
	dd('here star defined');
}

if ($currentDomain === static::$subdomain) {
	static::$defaultController = static::$subdomain;
	// dd('here default-');
}

--- Working
/**
 * Set subdomain for routes.
 *
 * @param string $subdomain The subdomain to set.
 * @param string|null $definedController The default controller to set. null by default.
 * @return static Returns a new instance of the class.
 */
public static function domain(string $subdomain, ?string $definedController = null)
{

	[$name,] = explode('.', $subdomain);

	static::$subdomain = $name;

	$currentDomain = (new static)->getCurrentSubdomain();

	// if ($definedController !== null) {
	// 	static::$definedController = $definedController;
	// }

	// if ($currentDomain === static::$subdomain) {
	// 	static::$defaultController = static::$subdomain;
	// }

	if (preg_match('/\{.*\}/', static::$subdomain)) {
		static::$subdomain = "*";
	}

	// dd((static::$subdomain === '*') && ($definedController === null) && SUBDOMAIN !== false);
	if (($currentDomain === static::$subdomain) && ($definedController !== null)) {
		static::$definedController = $definedController;
		// dd('here defined-');
	}

	if ((static::$subdomain === '*') && ($definedController !== null)) {
		static::$definedController = $definedController;
		$currentDomain = Route::dynamic();
		// dd('here star defined');
	}

	if ((static::$subdomain === '*') && ($definedController === null) && SUBDOMAIN !== false) {
		$currentDomain = static::$subdomain = Route::dynamic();
		// static::$subdomain = $currentDomain;
		// dd('here star-',(static::$subdomain === '*') && ($definedController === null) && SUBDOMAIN !== false);
		// dd('here star-');
	}
// dd($currentDomain === static::$subdomain);
	if ($currentDomain === static::$subdomain) {
		static::$defaultController = static::$subdomain;
		// dd('here default-');
	}

	// dd(static::$definedController);

	// dd(SUBDOMAIN, static::$definedController, $currentDomain, Route::dynamic(), static::$defaultController);


	// Returns a new instance of the class.
	return new static;
}


protected static function dynamic()
{
	// static::$prefix = $name;
	// call_user_func($callback);
	// static::$prefix = null;
	// static::$subdomain = null;
	return (new static)->getCurrentSubdomain();

}
---

function createSlug($string) {
    // Convert to lowercase
    $slug = strtolower($string);

    // Transliterate accented characters to ASCII
    $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);

    // Remove special characters
    $slug = preg_replace('/[^a-z0-9\s]/', '', $slug);

    // Replace spaces with hyphens
    $slug = str_replace(' ', '-', $slug);

    // Trim leading and trailing hyphens
    $slug = trim($slug, '-');

    return $slug;
}


---


```

Curl Class

https://github.com/php-mod/curl
https://github.com/nguyenanhung/simple-curl-request-helper
https://github.com/seikan/Cart

A PHP 8.3 version function

```php

if ( ! function_exists('is_php83'))
{
	/**
	 * Determines if the current version of PHP is 
	 * equal to or greater than the version 8.3
	 *
	 * @param	string
	 * @return	bool	true if the current version is $version or higher
	 */
	function is_php83()
	{
		static $php_83;
		
		$php_83 = floatval('8.3');

		if (floatval(PHP_VERSION) >= $php_83)
		{
			return true;
		}

		return false;
	}
}
```

```php
// change in DB_query_builder.php line 642		

    ${$qb_key} = ['condition' => $prefix . $k, 'value' => $v, 'escape' => $escape];
    $this->{$qb_key}[] = ${$qb_key};
    if ($this->qb_caching === true) {
        $this->{$qb_cache_key}[] = ${$qb_key};
        $this->qb_cache_exists[] = substr($qb_key, 3);
    }
```
// ------------------------------------------------------------------------

https://github.com/bcit-ci/CodeIgniter/pull/6136/commits/ea00b13ab1bf3a7029969797e4511379b9362746

https://github.com/bcit-ci/CodeIgniter/pull/6272/commits/16b42e628836fe8c3d3a87eb5ee7478bf803730d

https://stackoverflow.com/questions/18746811/splitting-apng-into-png-images-with-php

https://stackoverflow.com/questions/4525152/can-i-programmatically-determine-if-a-png-is-animated

https://stackoverflow.com/questions/55934447/how-can-i-programmatically-create-apng-files/69715802

https://github.com/selective-php/image-type

https://github.com/cerbero90/json-parser

https://github.com/simonhamp/the-og *** meta tags

https://github.com/ryangjchandler/stencil

https://github.com/ryangjchandler/container

https://github.com/ryangjchandler/advent-of-code

https://stackoverflow.com/questions/1995562/now-function-in-php

https://dev.to/gromnan/fix-php-84-deprecation-implicitly-marking-parameter-as-nullable-is-deprecated-the-explicit-nullable-type-must-be-used-instead-5gp3

### Git commits to complete push a webby version

```bash
git commit -s -m ":zap: bump webby version to v2.12.1"

git push // push current changes to git

git tag -a v2.12.0 -m ":zap: enable support for subdomains and fixes" // tag a new  
git push --tags // push all git tags
git tag -d v2.12.0 delete local tag
git push origin :v2.12.0 delete remote tag
```



```json

Summary#

"require": {
    "vendor/package": "1.3.2", // exactly 1.3.2

    // >, <, >=, <= | specify upper / lower bounds
    "vendor/package": ">=1.3.2", // anything above or equal to 1.3.2
    "vendor/package": "<1.3.2", // anything below 1.3.2

    // * | wildcard
    "vendor/package": "1.3.*", // >=1.3.0 <1.4.0

    // ~ | allows last digit specified to go up
    "vendor/package": "~1.3.2", // >=1.3.2 <1.4.0
    "vendor/package": "~1.3", // >=1.3.0 <2.0.0

    // ^ | doesn't allow breaking changes (major version fixed - following semver)
    "vendor/package": "^1.3.2", // >=1.3.2 <2.0.0
    "vendor/package": "^0.3.2", // >=0.3.2 <0.4.0 // except if major version is 0
}

// Try DES3 in OFB mode, just for the sake of changing stuff
		$this->encryption->initialize(array('cipher' => 'tripledes', 'mode' => 'ofb', 'key' => substr($key, 0, 8)));
		$this->assertEquals($message, $this->encryption->decrypt($this->encryption->encrypt($message)));
```

```php

<?php

if (isset($_GET['type'])) {
	switch($_GET['type']) {
	case 'json':
		outputJSON();
		break;
	case 'JSON':
		outputJSON();
		break;
	case 'text':
		outputTXT();
		break;
	case 'TEXT':
		outputTXT();
		break;
	default:
		outputTXT();
		break;
	}
} else {
	outputTXT();
}

function getIP() {
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    	$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

function outputJSON() {
	header('Content-Type: application/json');
	$output = json_encode(getIP());
	echo "[" . $output . "]";
}

function outputTXT() {
	header('Content-Type: text/plain');
	$output = getIP();
	echo $output . "\n";
}

// Build the resizing command
		switch ($this->image_type)
		{
			case IMAGETYPE_GIF :
				$cmd_in		= 'giftopnm';
				$cmd_out	= 'ppmtogif';
				break;
			case IMAGETYPE_JPEG :
				$cmd_in		= 'jpegtopnm';
				$cmd_out	= 'ppmtojpeg';
				break;
			case IMAGETYPE_PNG :
				$cmd_in		= 'pngtopnm';
				$cmd_out	= 'ppmtopng';
				break;
			/*case IMAGETYPE_WEBP :
				$cmd_in		= 'webptopnm';
				$cmd_in		= 'webptopnm';
				$cmd_out	= 'ppmtowebp';
				$cmd_out	= 'ppmtowebp';
				break;
				break;
			case IMAGETYPE_AVIF :
				$cmd_in		= 'webptopnm';
				$cmd_out	= 'ppmtowebp';
				break;*/
		}
```

```php

	/**
	 * Contexts provide a way for modules to assign controllers to an area of the
	 * site based on the name of the controller. This can be used for making a
	 * '/developer' area of the site that all modules can create functionality into.
	 *
	 * @param  string $name The name of the URL segment
	 * @param  string $controller The name of the controller
	 * @param  array $options
	 *
	 * @return void
	 */
	public static function context($name, $controller = null, $options = [], $hasController = true)
	{
		// If $controller is an array, then it's actually the options array,
		// so we'll reorganize parameters.
		if (is_array($controller)) {
			$options = $controller;
			$controller = null;
		}

		$name = str_replace('/', '.', $name);
		$name = explode('.', $name);
		$module = $name[0];
		$controller = !isset($name[1]) ? $module : $name[1];
		$name = str_replace('.', '/', implode('.', $name));

		$moc = static::setMOC($module, $controller, $hasController);

		// dd($name, $moc);
		// static::setRouteSignature($name, $method, $moc);

		// dd($module);

		// If $controller is empty, then we need to rename it to match
		// the $name value.
		if (empty($controller)) {
			$controller = $name;
		}

		$offset = isset($options['offset']) ? (int) $options['offset'] : 0;

		// Some helping hands
		$first = 1 + $offset;
		$second = 2 + $offset;
		$third = 3 + $offset;
		$fourth = 4 + $offset;
		$fifth = 5 + $offset;
		$sixth = 6 + $offset;

		static::any($name.'/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)', "\${$first}/{$controller}/\${$second}/\${$third}/\${$fourth}/\${$fifth}/\${$sixth}");
		static::any($name.'/(:any)/(:any)/(:any)/(:any)/(:any)', "\${$first}/{$controller}/\${$second}/\${$third}/\${$fourth}/\${$fifth}");
		static::any($name.'/(:any)/(:any)/(:any)/(:any)', "\${$first}/{$controller}/\${$second}/\${$third}/\${$fourth}");
		static::any($name.'/(:any)/(:any)/(:any)', "\${$first}/{$controller}/\${$second}/\${$third}");
		static::any($name.'/(:any)/(:any)', "\${$first}/{$controller}/\${$second}");
		static::any($name.'/(:any)', "\${$first}/{$controller}");

		unset($first, $second, $third, $fourth, $fifth, $sixth);

		// Are we creating a home controller?
		if (isset($options['home']) && !empty($options['home'])) {
			static::any($name, "{$options['home']}");
		}
		
	}

	<?php
/**
 * (c) Pierre-Henry Soria <hi@ph7.me>
 * MIT License <https://opensource.org/licenses/MIT>
 */

namespace PH7\Datatype;

/**
 * @link https://www.w3schools.com/php/php_datatypes.asp
 */
final class Type
{
    public const BOOLEAN = 'boolean';
    public const BOOL = 'bool';
    public const INT = 'int';
    public const INTEGER = 'integer';
    public const FLOAT = 'float';
    public const DOUBLE = 'double'; // "float" should be used instead
    public const STRING = 'string';
    public const ARRAY = 'array';
    public const OBJECT = 'object';
    public const NULL = 'null';
}
```
* Changes made in DotEnvWriter and Console in Tonwoho, ssl-serve file also added

Creativity is allowing yourself to make mistakes. Design is knowing which ones to keep.

<div class="p-6"><img src="/avatar.jpg" alt="Justin Case" class="w-32 aspect-square object-cover rounded-full border-2 border-primary shadow-avatar"></div>

VennDev/Vapm: A library support for PHP about Async, Promise, Coroutine, Thread, GreenThread and other non-blocking methods. The method is based on Fibers & Generator & Processes, requires you to have php version from >= 8.1
https://github.com/VennDev/Vapm

VennDev/VapmDatabase: - Async Database for PHP
https://github.com/VennDev/VapmDatabase/tree/main

halokid/CodeIgniter-2.x-mysql-async-query: mysql async query support for CI 2.x
https://github.com/halokid/CodeIgniter-2.x-mysql-async-query

Next update? | Vapm
https://venndev.gitbook.io/vapm/about/next-update

https://wpmayor.com/htmx-might-be-a-big-deal-for-wordpress/

https://dev.to/ismaelfi/my-tech-stack-as-an-indie-hacker-1ie

https://dev.to/quietnoisemaker/building-a-simple-referral-system-api-with-laravel-11-2aom

https://github.com/kcal-app

https://techsouce.com/laravel-crud-tutorial-for-beginners/


dodistyo/ci-rest-jwt: Codeigniter REST API using JWT for Authentication
https://github.com/dodistyo/ci-rest-jwt

Virtuallified/REST-Api_JWT_CodeIgniter3: CodeIgniter 3 - REST API Integration with JWT (JSON Web Tokens)
https://github.com/Virtuallified/REST-Api_JWT_CodeIgniter3

https://gist.github.com/code-boxx/723160fa409c04f76b53e38fa9e2f427

ab -n 1000 -c 10 -v2 -l http://clinicadecot.org/rehabilitacion

wrk -t12 -c400 -d30s http://local.io:8000


benchmarks

wrk -t12 -c400 -d30s http://local.io:8000
Running 30s test @ http://local.io:8000
  12 threads and 400 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency     1.03s   590.06ms   2.00s    56.55%
    Req/Sec    27.54     18.04    90.00     75.37%
  2229 requests in 30.07s, 74.32MB read
  Socket errors: connect 0, read 2229, write 0, timeout 2084
Requests/sec:     74.13
Transfer/sec:      2.47MB


wrk -t12 -c400 -d30s http://local.io:8085
Running 30s test @ http://local.io:8085
  12 threads and 400 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency     1.08s   148.30ms   1.26s    92.55%
    Req/Sec    82.25     82.47   323.00     77.83%
  10791 requests in 30.08s, 138.43MB read
  Socket errors: connect 0, read 10791, write 0, timeout 0
Requests/sec:    358.78
Transfer/sec:      4.60MB



ab -n 1000 -c400 http://local.io:8000/
Server Software:        
Server Hostname:        local.io
Server Port:            8000

Document Path:          /
Document Length:        33870 bytes

Concurrency Level:      400
Time taken for tests:   12.720 seconds
Complete requests:      1000
Failed requests:        106
   (Connect: 0, Receive: 0, Length: 106, Exceptions: 0)
Total transferred:      35001893 bytes
HTML transferred:       33869893 bytes
Requests per second:    78.62 [#/sec] (mean)
Time per request:       5088.011 [ms] (mean)
Time per request:       12.720 [ms] (mean, across all concurrent requests)
Transfer rate:          2687.22 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    4   5.6      0      16
Processing:    12 4063 1564.8   5019    5181
Waiting:       12 4062 1564.8   5018    5181
Total:         28 4067 1559.5   5019    5182

Percentage of the requests served within a certain time (ms)
  50%   5019
  66%   5041
  75%   5117
  80%   5124
  90%   5159
  95%   5170
  98%   5173
  99%   5176
 100%   5182 (longest request)


ab -n 1000 -c400 http://local.io:8085/
Server Software:        
Server Hostname:        local.io
Server Port:            8085

Document Path:          /
Document Length:        13194 bytes

Concurrency Level:      400
Time taken for tests:   2.508 seconds
Complete requests:      1000
Failed requests:        0
Total transferred:      13450000 bytes
HTML transferred:       13194000 bytes
Requests per second:    398.69 [#/sec] (mean)
Time per request:       1003.288 [ms] (mean)
Time per request:       2.508 [ms] (mean, across all concurrent requests)
Transfer rate:          5236.69 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    6   7.7      0      21
Processing:     8  797 310.5    990    1019
Waiting:        3  797 310.6    990    1019
Total:         29  803 303.3    991    1019

Percentage of the requests served within a certain time (ms)
  50%    991
  66%    998
  75%   1006
  80%   1008
  90%   1013
  95%   1015
  98%   1017
  99%   1017
 100%   1019 (longest request)



 // Laravel raw welcome view

 Running 30s test @ http://local.io:8000
  12 threads and 400 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency     1.01s   569.46ms   1.99s    58.06%
    Req/Sec    27.51     18.66    90.00     76.67%
  2142 requests in 30.09s, 71.33MB read
  Socket errors: connect 0, read 2142, write 0, timeout 1987
Requests/sec:     71.19
Transfer/sec:      2.37MB


Server Software:        
Server Hostname:        local.io
Server Port:            8000

Document Path:          /
Document Length:        33822 bytes

Concurrency Level:      400
Time taken for tests:   12.900 seconds
Complete requests:      1000
Failed requests:        0
Total transferred:      34954000 bytes
HTML transferred:       33822000 bytes
Requests per second:    77.52 [#/sec] (mean)
Time per request:       5159.972 [ms] (mean)
Time per request:       12.900 [ms] (mean, across all concurrent requests)
Transfer rate:          2646.12 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    5   6.8      0      18
Processing:    13 4148 1619.3   5152    5380
Waiting:       12 4147 1619.3   5151    5380
Total:         31 4153 1612.8   5152    5380

Percentage of the requests served within a certain time (ms)
  50%   5152
  66%   5202
  75%   5220
  80%   5229
  90%   5322
  95%   5346
  98%   5369
  99%   5371
 100%   5380 (longest request)

https://medium.com/@laravelprotips/building-an-e-commerce-database-in-laravel-structuring-products-variants-and-attributes-9e48e0337498
https://gist.github.com/graceman9/4335154c2a25d1c3de67a88bcf522cbf
https://dev.to/mainick/dto-vs-vo-in-php-4adi?ref=dailydev
https://medium.com/@mrcyna/concurrency-in-php-826cbd733549