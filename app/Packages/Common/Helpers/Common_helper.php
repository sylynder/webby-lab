<?php

use Dompdf\Dompdf;
use Dompdf\Options;

if ( ! function_exists('create_array'))
{
	/**
	 *  Create an array from another
	 *
	 *  @param     array     $array
	 *  @return    object
	 */
	function create_array($array, array $structure)
	{
        $make = array_map(function($array) use ($structure) {
            return $structure;
        }, $array);

        return $make;

	}
}

if ( ! function_exists('child_protect')) {
    /**
     * Check date against age
     *
     * @param  string  $date
     * @param  int  $age
     * @return bool
     */
    function child_protect($date, $age = 15)
    {
        $currentDate = today();
        $dateDifference = date_diff(date_create($date), date_create($currentDate));
        $years = $dateDifference->y;
        
        if ($years < $age) {
            return false; // Date is outside the age limit
        } else {
            return true; // Date is within the age limit
        }
    }
}

if ( ! function_exists('generate_max_id'))
{

	/**
	 * Generate IDs using max_id() function
	 *
	 * @param string $table
	 * @param string $prefix
	 * @param integer $length
	 * @param string $select_as
	 * @return string
	 */	
	function generate_max_id($table, $prefix = '', $length = 6, $select_as = null)
	{
        $total = max_id($table, $select_as);

        if ($total == null) {
            $total = 0;
        }

        return $prefix . pad_left('0', (int)($total + 1), $length);
	}
}

if ( ! function_exists('valid_human_url'))
{
	/**
	 * Validate URL
	 *
	 * @param string $url
	 * @return bool
	 */
	function valid_human_url($url)
	{
		$pattern = "/^(?:(?:https?|ftp):\/\/)?(?:www\.)?([^\s\/$.?#].[^\s]*)$/i";

		if (!preg_match($pattern, $url)) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists('real_url'))
{
	/**
	 * Real URL
	 *
	 * @param    string
	 * @return    string
	 */
	function real_url($url)
	{
		return @fsockopen("$url", 80, $errno, $errstr, 30);
	}
}

if ( ! function_exists('create_pdf'))
{
	/**
	 * Create PDF with DomPdf
	 *
	 * @param string $html
	 * @param string $filename
	 * @param boolean $download
	 * @param string $paper
	 * @param string $orientation
	 * @return mixed
	 */
	function create_pdf($html, $filename='', $download=true, $paper='A4', $orientation='portrait')
	{
		// Set options to enable embedded PHP 
		$options = new Options(); 
		$options->set('isPhpEnabled', 'true'); 
		
		// Instantiate dompdf class 
		$dompdf = new Dompdf($options); 

        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();

        if ($download) 
            $dompdf->stream($filename.'.pdf', ['Attachment' => 1]);
        else
            $dompdf->stream($filename.'.pdf', ['Attachment' => 0]);
    }
}

if ( ! function_exists('compare_date'))
{
	/**
	 * Compare two dates
	 *
	 * @param string $start_date
	 * @param string $end_date
	 * @return bool
	 */
	function compare_date($start_date, $end_date) {

		$start_date = strtotime($start_date);
		$end_date = strtotime($end_date);

		if ($end_date >= $start_date) {
			return true;
		}	
		
		return false;
	}
}

if ( ! function_exists('is_checked'))
{
	/**
	 * Verify if checked
	 *
	 * @param int $key
	 * @param int $value
	 * @return bool
	 */
	function is_checked($key, $value = ONE)
	{
		return ($key == $value) ? true : false;
	}
}

if ( ! function_exists('is'))
{
	/**
	 *  'Is' function
	 *
	 *  @param     string     $key
	 *  @param     string     $value
	 *  @return    boolean
	 */
	function is($key, $value = NULL)
	{
		$common		= ['https', 'cli', 'php', 'writable'];
		$useragent	= ['browser', 'mobile', 'referral', 'robot'];

		if (in_array($key, $useragent))
		{
			return app('user_agent')->{'is_'.$key}($value);
		}

		if (in_array($key, $common))
		{
			$function = ($key == 'writable')
				? 'is_really_writable'
				: 'is_'.$key;

			return $function($value);
		}

		if ($key == 'ajax')
		{
			return app('input')->is_ajax_request();
		}

		if ($key == 'post')
		{
			return (app('input')->server('REQUEST_METHOD') === 'POST');
		}

		if ($key == 'get')
		{
			return (app('input')->server('REQUEST_METHOD') === 'GET');
		}

		if ($key == 'loaded' OR $key == 'load')
		{
			return (bool) app('load')->is_loaded($value);
		}

		return false;
	}
}

if ( ! function_exists( 'list_regions' )) 
{

    function list_regions($filter = null, $select = "id, code, name")
    {

		if ($select != null) {
			return use_table('regions')->select($select)->get($filter);
		}

		return use_table('regions')->get($filter);
	}
}

if ( ! function_exists( 'list_years' )) 
{

    function list_years($start_year = 1930, $current_year = null)
	{
		$current_year = $current_year ?? date('Y');
		$start_year = $start_year ?? 1930;

		$list = [];

		for ($year = $current_year; $year >= $start_year; $year--) {
			$list[] = $year;
		}

		return $list;
	}
}

if ( ! function_exists( 'list_industries' )) 
{

    function list_industries() 
	{
		$list = [
			"Advertising",
			"Manufacturing",
			"Marketing",
			"Sales",
			"Technology",
			"Other"
		];

		$customOrder = [
			"Manufacturing" => 2,
			"Marketing" => 3,
			"Sales" => 4,
			"Technology" => 5,
			"Other" => 1,
			"Advertising" => 7
		];

		// Sort the array based on the custom order
		usort($list, function ($left, $right) use ($customOrder) {
			return $customOrder[$left] - $customOrder[$right];
		});

		return $list;
	}

}

if ( ! function_exists( 'list_career_levels' )) 
{

    function list_career_levels() 
	{
		$list = [
			"Entry",
			"Beginner",
			"Intermediate",
			"Advanced",
			"Expert",
			"Highly Skilled",
			"Specialist"
		];

		$customOrder = [
			"Entry" => 1,
			"Beginner" => 2,
			"Intermediate" => 3,
			"Advanced" => 4,
			"Expert" => 5,
			"Highly Skilled" => 6,
			"Specialist" => 7,
		];

		// Sort the array based on the custom order
		usort($list, function ($left, $right) use ($customOrder) {
			return $customOrder[$left] - $customOrder[$right];
		});

		return $list;
	}

}

if ( ! function_exists( 'load_asset' )) 
{

    function load_asset($asset = '', $is_image = true)
    {

        if (empty($asset) && $is_image === true) {
            $asset = DEFAULT_LOGO;
        }

        $exists = file_exists($asset);

        if ($exists) {
            return load_path($asset);
        }
		
        $exists = file_exists(APP_ASSETS_PATH . $asset);

        if ($exists) {
            return load_path(APP_ASSETS_PATH . $asset);
        }

        return load_path(DEFAULT_LOGO);

    }
}

if ( ! function_exists('read_time')) 
{

    /**
     * Calculate reading time of a given string.
     * 
     * @param string $text The string to calculate reading time for.
     * @param string $wpm The rate of words per minute to use.
     * @return array|string
     */
    function read_time($text, $wpm = 240, $default_suffix = true)
    {
        $total_words = str_word_count(strip_tags($text));
        
        $read_time = ceil($total_words / $wpm);

        if ($read_time == 1) {
            $suffix = ' minute read';
        } else {
            $suffix = ' minutes read';
        }

		if ($default_suffix === false) {
			return $read_time;
		}

        return $read_time . $suffix;
    }
}

if ( ! function_exists('month_list')) 
{
	
	/**
	 * Pick months with a given list
	 *
	 * @param array $list
	 * @param boolean $pluck
	 * @return mixed
	 */
	function month_list($list = [], $with = 'name', $pluck = false)
	{
		$monthList = [
			'January', 'February', 'March', 'April', 'May', 'June', 'July',
			'August', 'September', 'October', 'November', 'December' 
		];

		$count = 0;
		$months = array_map(function ($monthList) use(&$count) {
			$count++;
			return [
				'number' => $count,
				'name' => $monthList
			];
		}, $monthList);

		if (empty($list)) {
			return $months;
		}

		if ($pluck) {
			return arrayz($months)->whereIn('name', $list)->pluck($with)->toArray();
		}

		return arrayz($months)->whereIn('name', $list)->pick($with)->get();
		
	}
}

if ( ! function_exists('initials')) 
{
	/**
	 * Get initials of a given name
	 *
	 * @param string $str
	 * @return string
	 */
	function initials($str) {

		$str = limit_words($str, 2);
		
		$ret = '';

		if (empty($str)) {
			return $ret;
		}

		foreach (explode(' ', $str) as $word)
			$ret .= strtoupper($word[0]);
		return $ret;
	}
}
