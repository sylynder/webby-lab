<?php

// ------------------------------------------------------------------------

if ( ! function_exists('action_check')) 
{
    /**
     * Check if permission action exists
     * For css beautification
     */
    function action_check($successCssClass = 'fa-check text-primary', $failureCssClass = 'fa-times text-black-100', ...$params)
    {
        [$action, $permission_actions] = $params;

        return action_exists($action, $permission_actions, $successCssClass, $failureCssClass);
    }
}

if ( ! function_exists('array_pluck')) {
    /**
     *  Pluck an array of values from an array
     *
     *  @param     array     $array
     *  @param     string    $value
     *  @param     string    $key
     *  @return    array
     */
    function array_pluck($array, $value, $key = NULL)
    {
        $results = [];

        foreach ($array as $item) {
            
            $item_value = data_get($item, $value);

            //	If the key is "null", we will just append the value to
            //	the array and keep looping. Otherwise we will key the
            //	array using the value of the key we received from the
            //	developer. Then we'll return the final array form.

            if (is_null($key)) {
                $results[] = $item_value;
            } else {
                $item_key = data_get($item, $key);

                $results[$item_key] = $item_value;
            }
        }

        return $results;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('data_get')) {
    /**
     *  Get an item from an array or object using 'dot' notation
     *
     *  @param     mixed     $target
     *  @param     string    $key
     *  @param     mixed     $default
     *  @return    mixed
     */
    function data_get($target, $key, $default = NULL)
    {
        if (is_null($key)) {
            return $target;
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($target)) {
                if (!array_key_exists($segment, $target)) {
                    return value($default);
                }

                $target = $target[$segment];
            } elseif ($target instanceof ArrayAccess) {
                if (!isset($target[$segment])) {
                    return value($default);
                }

                $target = $target[$segment];
            } elseif (is_object($target)) {
                if (!isset($target->{$segment})) {
                    return value($default);
                }

                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }

        return $target;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('value')) {
    /**
     *  Return the default value of the given value
     *
     *  @param     mixed    $value
     *  @return    mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}
