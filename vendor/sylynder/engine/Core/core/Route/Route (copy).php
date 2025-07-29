<?php

/**
 * Intelligent, Elegant routing for Webby
 *
 * Inspired by Jamie Rumblelow's Pigeon Route and Bonfire Route
 * 
 * I decided to implement it and made
 * much modification to work with Webby
 * 
 * @author Kwame Oteng Appiah-Nti (Developer Kwame)
 * 
 */

namespace Base\Route;

use Closure;
use Base\Helpers\Inflector;

class RouteNew
{


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

		static::any($name . '/(:any)/(:any)/(:any)/(:any)/(:any)/(:any)', "\${$first}/{$controller}/\${$second}/\${$third}/\${$fourth}/\${$fifth}/\${$sixth}");
		static::any($name . '/(:any)/(:any)/(:any)/(:any)/(:any)', "\${$first}/{$controller}/\${$second}/\${$third}/\${$fourth}/\${$fifth}");
		static::any($name . '/(:any)/(:any)/(:any)/(:any)', "\${$first}/{$controller}/\${$second}/\${$third}/\${$fourth}");
		static::any($name . '/(:any)/(:any)/(:any)', "\${$first}/{$controller}/\${$second}/\${$third}");
		static::any($name . '/(:any)/(:any)', "\${$first}/{$controller}/\${$second}");
		static::any($name . '/(:any)', "\${$first}/{$controller}");

		unset($first, $second, $third, $fourth, $fifth, $sixth);

		// Are we creating a home controller?
		if (isset($options['home']) && !empty($options['home'])) {
			static::any($name, "{$options['home']}");
		}
	}
    
	/**
     * Sets the HX-Location to redirect
     * without reloading the whole page
	 * from CI_Output class.
     */
    public function hxLocation(
        string $path,
        ?string $source = null,
        ?string $event = null,
        ?string $target = null,
        ?string $swap = null,
        ?array $values = null,
        ?array $headers = null
    ) {
		return app('Output')->hxLocation($path, $source, $event, $target, $swap, $values, $headers);
	}

	/**
     * Sets the HX-Redirect to URI to redirect to
	 * from CI_Output class.
     *
     * @param string $uri The URI to redirect to
     */
    public function hxRedirect(string $uri)
	{
		return app('Output')->hxRedirect($uri);
	}

	/**
     * Sets the HX-Refresh to true from CI_Output class.
     */
    public function hxRefresh()
    {
        return app('Output')->hxRefresh();
    }

}
