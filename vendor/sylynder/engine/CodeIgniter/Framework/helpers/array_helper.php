<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2019, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Array Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/userguide3/helpers/array_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('element'))
{
	/**
	 * Element
	 *
	 * Lets you determine whether an array index is set and whether it has a value.
	 * If the element is empty it returns null (or whatever you specify as the default value.)
	 *
	 * @param	string
	 * @param	array
	 * @param	mixed
	 * @return	mixed	depends on what the array contains
	 */
	function element($item, array $array, $default = null)
	{
		return array_key_exists($item, $array) ? $array[$item] : $default;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('random_element'))
{
	/**
	 * Random Element - Takes an array as input and returns a random element
	 *
	 * @param	array
	 * @return	mixed	depends on what the array contains
	 */
	function random_element($array)
	{
		return is_array($array) ? $array[array_rand($array)] : $array;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('elements'))
{
	/**
	 * Elements
	 *
	 * Returns only the array items specified. Will return a default value if
	 * it is not set.
	 *
	 * @param	array
	 * @param	array
	 * @param	mixed
	 * @return	mixed	depends on what the array contains
	 */
	function elements($items, array $array, $default = null)
	{
		$return = [];

		is_array($items) OR $items = [$items];

		foreach ($items as $item)
		{
			$return[$item] = array_key_exists($item, $array) ? $array[$item] : $default;
		}

		return $return;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_add'))
{
	/**
	 *  Add an element to an array using 'dot' notation if it doesn't exist
	 *
	 *  @param     array     $array
	 *  @param     string    $key
	 *  @param     mixed     $value
	 *  @return    array
	 */
	function array_add($array, $key, $value)
	{
		if (is_null(get($array, $key)))
		{
			set($array, $key, $value);
		}

		return $array;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_collapse'))
{
	/**
	 *  Collapse an array of arrays into a single array
	 *
	 *  @param     array    $array
	 *  @return    array
	 */
	function array_collapse($array)
	{
		$results = [];

		foreach ($array as $values)
		{
			if ( ! is_array($values))
			{
				continue;
			}

			$results = array_merge($results, $values);
		}

		return $results;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_divide'))
{
	/**
	 *  Divide an array into two arrays, one with keys and the other with values
	 *
	 *  @param     array    $array
	 *  @return    array
	 */
	function array_divide($array)
	{
		return [array_keys($array), array_values($array)];
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_dot'))
{
	/**
	 *  Flatten a multi-dimensional associative array with dots
	 *
	 *  @param     array     $array
	 *  @param     string    $prepend
	 *  @return    array
	 */
	function array_dot($array, $prepend = '')
	{
		$results = [];

		foreach ($array as $key => $value)
		{
			if (is_array($value))
			{
				$results = array_merge($results, dot($value, $prepend.$key.'.'));
			}
			else
			{
				$results[$prepend.$key] = $value;
			}
		}

		return $results;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_except'))
{
	/**
	 *  Get all of the given array except for a specified array of items
	 *
	 *  @param     array           $array
	 *  @param     array|string    $keys
	 *  @return    array
	 */
	function array_except($array, $keys)
	{
		return array_diff_key($array, array_flip((array) $keys));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_first'))
{
	/**
	 *  Return the first element in an array passing a given truth test
	 *
	 *  @param     array       $array
	 *  @param     \Closure    $callback
	 *  @param     mixed       $default
	 *  @return    mixed
	 */
	function array_first($array, $callback, $default = null)
	{
		foreach ($array as $key => $value)
		{
			if (call_user_func($callback, $key, $value))
			{
				return $value;
			}
		}

		return value($default);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_flatten'))
{
	/**
	 *  Flatten a multi-dimensional array into a single level
	 *
	 *  @param     array    $array
	 *  @return    array
	 */
	function array_flatten($array)
	{
		$return = [];

		array_walk_recursive($array, function($x) use (&$return)
		{
			$return[] = $x;
		});

		return $return;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_forget'))
{
	/**
	 *  Remove one or many array items from a given array using 'dot' notation
	 *
	 *  @param     array           $array
	 *  @param     array|string    $keys
	 *  @return    void
	 */
	function array_forget(&$array, $keys)
	{
		$original =& $array;

		foreach ((array) $keys as $key)
		{
			$parts = explode('.', $key);

			while (count($parts) > 1)
			{
				$part = array_shift($parts);

				if (isset($array[$part]) && is_array($array[$part]))
				{
					$array =& $array[$part];
				}
			}

			unset($array[array_shift($parts)]);

			// clean up after each pass
			$array =& $original;
		}
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_get'))
{
	/**
	 *  Get an item from an array using 'dot' notation
	 *
	 *  @param     array     $array
	 *  @param     string    $key
	 *  @param     mixed     $default
	 *  @return    mixed
	 */
	function array_get($array, $key, $default = null)
	{
		if (is_null($key))
		{
			return $array;
		}

		if (isset($array[$key]))
		{
			return $array[$key];
		}

		foreach (explode('.', $key) as $segment)
		{
			if ( ! is_array($array) OR ! array_key_exists($segment, $array))
			{
				return value($default);
			}

			$array = $array[$segment];
		}

		return $array;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_has'))
{
	/**
	 *  Check if an item or items exist in an array using 'dot' notation
	 *
	 *  @param     array     $array
	 *  @param     string    $key
	 *  @return    boolean
	 */
	function array_has($array, $key)
	{
		if (empty($array) OR is_null($key))
		{
			return false;
		}

		if (array_key_exists($key, $array))
		{
			return true;
		}

		foreach (explode('.', $key) as $segment)
		{
			if ( ! is_array($array) OR ! array_key_exists($segment, $array))
			{
				return false;
			}

			$array = $array[$segment];
		}

		return true;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_last'))
{
	/**
	 *  Return the last element in an array passing a given truth test
	 *
	 *  @param     array       $array
	 *  @param     \Closure    $callback
	 *  @param     mixed       $default
	 *  @return    mixed
	 */
	function array_last($array, $callback, $default = null)
	{
		return first(array_reverse($array), $callback, $default);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_only'))
{
	/**
	 *  Get a subset of the items from the given array
	 *
	 *  @param     array           $array
	 *  @param     array|string    $keys
	 *  @return    array
	 */
	function array_only($array, $keys)
	{
		return array_intersect_key($array, array_flip((array) $keys));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_pluck'))
{
	/**
	 *  Pluck an array of values from an array
	 *
	 *  @param     array     $array
	 *  @param     string    $value
	 *  @param     string    $key
	 *  @return    array
	 */
	function array_pluck($array, $value, $key = null)
	{
		$results = [];

		foreach ($array as $item)
		{
			$item_value = data_get($item, $value);

			//	If the key is "null", we will just append the value to
			//	the array and keep looping. Otherwise we will key the
			//	array using the value of the key we received from the
			//	developer. Then we'll return the final array form.

			if (is_null($key))
			{
				$results[] = $item_value;
			}
			else
			{
				$item_key = data_get($item, $key);

				$results[$item_key] = $item_value;
			}
		}

		return $results;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_prepend'))
{
	/**
	 *  Push an item onto the beginning of an array
	 *
	 *  @param     array    $array
	 *  @param     mixed    $value
	 *  @param     mixed    $key
	 *  @return    array
	 */
	function array_prepend($array, $value, $key = null)
	{
		if (is_null($key))
		{
			array_unshift($array, $value);
		}
		else
		{
			$array = [$key => $value] + $array;
		}

		return $array;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_pull'))
{
	/**
	 *  Get a value from the array, and remove it
	 *
	 *  @param     array     &$array
	 *  @param     string    $key
	 *  @param     mixed     $default
	 *  @return    mixed
	 */
	function array_pull(&$array, $key, $default = null)
	{
		$value = get($array, $key, $default);

		forget($array, $key);

		return $value;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_random'))
{
	/**
	 *  Get a random value from an array
	 *
	 *  @param     array           $array
	 *  @param     integer|null    $amount
	 *  @return    mixed
	 */
	function array_random($array, $amount = null)
	{
		if (($amount ?: 1) > count($array))
		{
			return false;
		}

		if (is_null($amount))
		{
			return $array[array_rand($array)];
		}

		$keys		= array_rand($array, $amount);
		$results	= [];

		foreach ((array) $keys as $key)
		{
			$results[] = $array[$key];
		}

		return $results;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_set'))
{
	/**
	 *  Set an array item to a given value using 'dot' notation
	 *
	 *  @param     array     $array
	 *  @param     string    $key
	 *  @param     mixed     $value
	 *  @return    mixed
	 */
	function array_set(&$array, $key, $value)
	{
		//	If no key is given to the method, the entire array will be replaced
		if (is_null($key))
		{
			return $array = $value;
		}

		$keys = explode('.', $key);

		while (count($keys) > 1)
		{
			$key = array_shift($keys);

			//	If the key doesn't exist at this depth, we will just create
			//	an empty array to hold the next value, allowing us to create
			//	the arrays to hold final values at the correct depth. Then
			//	we'll keep digging into the array.
			if ( ! isset($array[$key]) OR ! is_array($array[$key]))
			{
				$array[$key] = [];
			}

			$array =& $array[$key];
		}

		$array[array_shift($keys)] = $value;

		return $array;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_sort_recursive'))
{
	/**
	 *  Recursively sort an array by keys and values
	 *
	 *  @param     array    $array
	 *  @return    array
	 */
	function array_sort_recursive($array)
	{
		foreach ($array as &$value)
		{
			if (is_array($value))
			{
				$value = array_sort_recursive($value);
			}
		}

		if (array_keys(array_keys($array)) !== array_keys($array))
		{
			ksort($array);
		}
		else
		{
			sort($array);
		}

		return $array;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_where'))
{
	/**
	 *  Filter the array using the given callback
	 *
	 *  @param     array       $array
	 *  @param     \Closure    $callback
	 *  @return    array
	 */
	function array_where($array, callable $callback)
	{
		$filtered = [];

		foreach ($array as $key => $value)
		{
			if (call_user_func($callback, $key, $value))
			{
				$filtered[$key] = $value;
			}
		}

		return $filtered;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_wrap'))
{
	/**
	 *  If the given value is not an array, wrap it in one
	 *
	 *  @param     mixed    $value
	 *  @return    array
	 */
	function array_wrap($value)
	{
		return is_array($value) ? $value :[$value];
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('data_get'))
{
	/**
	 *  Get an item from an array or object using 'dot' notation
	 *
	 *  @param     mixed     $target
	 *  @param     string    $key
	 *  @param     mixed     $default
	 *  @return    mixed
	 */
	function data_get($target, $key, $default = null)
	{
		if (is_null($key))
		{
			return $target;
		}

		foreach (explode('.', $key) as $segment)
		{
			if (is_array($target))
			{
				if ( ! array_key_exists($segment, $target))
				{
					return value($default);
				}

				$target = $target[$segment];
			}
			elseif ($target instanceof ArrayAccess)
			{
				if ( ! isset($target[$segment]))
				{
					return value($default);
				}

				$target = $target[$segment];
			}
			elseif (is_object($target))
			{
				if ( ! isset($target->{$segment}))
				{
					return value($default);
				}

				$target = $target->{$segment};
			}
			else
			{
				return value($default);
			}
		}

		return $target;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('first'))
{
	/**
	 *  Return the first element in an array passing a given truth test
	 *
	 *  @param     array       $array
	 *  @param     \Closure    $callback
	 *  @param     mixed       $default
	 *  @return    mixed
	 */
	function first($array, callable $callback, $default = null)
	{
		foreach ($array as $key => $value)
		{
			if (call_user_func($callback, $key, $value))
			{
				return $value;
			}
		}

		return value($default);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('forget'))
{
	/**
	 *  Remove one or many array items from a given array using 'dot' notation
	 *
	 *  @param     array           $array
	 *  @param     array|string    $keys
	 *  @return    void
	 */
	function forget(&$array, $keys)
	{
		$original =& $array;

		foreach ((array) $keys as $key)
		{
			$parts = explode('.', $key);

			while (count($parts) > 1)
			{
				$part = array_shift($parts);

				if (isset($array[$part]) && is_array($array[$part]))
				{
					$array =& $array[$part];
				}
			}

			unset($array[array_shift($parts)]);

			//	Clean up after each pass
			$array =& $original;
		}
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('get'))
{
	/**
	 *  Get an item from an array using 'dot' notation
	 *
	 *  @param     array     $array
	 *  @param     string    $key
	 *  @param     mixed     $default
	 *  @return    mixed
	 */
	function get($array, $key, $default = null)
	{
		if (is_null($key))
		{
			return $array;
		}

		if (isset($array[$key]))
		{
			return $array[$key];
		}

		foreach (explode('.', $key) as $segment)
		{
			if ( ! is_array($array) OR ! array_key_exists($segment, $array))
			{
				return value($default);
			}

			$array = $array[$segment];
		}

		return $array;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('head'))
{
	/**
	 *  Get the first element of an array (useful for method chaining)
	 *
	 *  @param     array    $array
	 *  @return    mixed
	 */
	function head($array)
	{
		return reset($array);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('last'))
{
	/**
	 *  Get the last element from an array
	 *
	 *  @param     array    $array
	 *  @return    mixed
	 */
	function last($array)
	{
		return end($array);
	}
}

if ( ! function_exists('object_get'))
{
	/**
	 *  Get an item from an object using 'dot' notation
	 *
	 *  @param     object    $object
	 *  @param     string    $key
	 *  @param     mixed     $default
	 *  @return    mixed
	 */
	function object_get($object, $key, $default = null)
	{
		if (is_null($key) OR trim($key) == '')
		{
			return $object;
		}

		foreach (explode('.', $key) as $segment)
		{
			if ( ! is_object($object) OR ! isset($object->{$segment}))
			{
				return value($default);
			}

			$object = $object->{$segment};
		}

		return $object;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('set'))
{
	/**
	 *  Set an array item to a given value using 'dot' notation
	 *
	 *  @param    array     $array
	 *  @param    string    $key
	 *  @param    mixed     $value
	 *  @return   array
	 */
	function set(&$array, $key, $value)
	{
		//	If no key is given to the method, the entire array will be replaced
		if (is_null($key))
		{
			return $array = $value;
		}

		$keys = explode('.', $key);

		while (count($keys) > 1)
		{
			$key = array_shift($keys);

			//	If the key doesn't exist at this depth, we will just create
			//	an empty array to hold the next value, allowing us to create
			//	the arrays to hold final values at the correct depth. Then
			//	we'll keep digging into the array.
			if ( ! isset($array[$key]) OR ! is_array($array[$key]))
			{
				$array[$key] = [];
			}

			$array =& $array[$key];
		}

		$array[array_shift($keys)] = $value;

		return $array;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('max_val_array'))
{
	/**
	 *  Return the max value fron array by key
	 *
	 *  @param     mixed    $object
	 *  @return    mixed
	 */
	function max_val_array($array, $keyToSearch)
	{
	    $currentMax = null;
	    foreach($array as $arr)
	    {
	        foreach($arr as $key => $value)
	        {
	            if ($key == $keyToSearch && ($value >= $currentMax))
	            {
	                $currentMax = $value;
	            }
	        }
	    }

	    return $currentMax;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('min_val_array'))
{
	/**
	 *  Return the min value fron array by key
	 *
	 *  @param     mixed    $object
	 *  @return    mixed
	 */
	function min_val_array($array, $keyToSearch){ 
	    $min = []; 
	    foreach ($array as $val) { 
	        if (!isset($val[$keyToSearch]) and is_array($val)) { 
	            $min2 = min_by_key($val, $keyToSearch); 
	            $min[$min2] = 1; 
	        } elseif (!isset($val[$keyToSearch]) and !is_array($val)) { 
	            return false; 
	        } elseif (isset($val[$keyToSearch])) { 
	            $min[$val[$keyToSearch]] = 1; 
	        } 
	    } 
	    return min( array_keys($min) ); 
	} 
}

// ------------------------------------------------------------------------

if ( ! function_exists('min_by_key'))
{
	/**
	 *  Return the min value fron array by key
	 *
	 *  @param     mixed    $object
	 *  @return    mixed
	 */
	function min_by_key($val, $keyToSearch){ 
	    $min = []; 
	    return min( array_keys($min) ); 
	} 
}

// ------------------------------------------------------------------------

if ( ! function_exists('min_max_array'))
{
	/**
	 *  Return an array with min and max value from array by key
	 *
	 *  @param     mixed    $object
	 *  @return    mixed
	 */
	function min_max_array($array, $keyToSearch) { 
	    return ['min' => min_val_array($array, $keyToSearch), 'max'=> max_val_array($array, $keyToSearch)]; 
	} 
}
