<?php

class RateLimit
{
    /**
     * Limit Requests By IP
     * 
     * limit requests for IP to $n number of 
     * requests per $n number of seconds
     *
     * @param string $request
     * @param integer $maxRequests
     * @param integer $seconds
     * @return void
     */
    public static function ipRequests(string $request, $maxRequests = 100, $seconds = 300)
    {

        $cacheKey = $request . "_key";
        $cacheRemainTime = $request . "_tmp";

        $currentTime = date("Y-m-d H:i:s");

        // if it's first request
        if (cache()->cacheItem($cacheKey) == null) {

            $currentTimePlus = date("Y-m-d H:i:s", strtotime("+".$seconds." seconds"));
            
            // First cache expiration check
            cache()->expireAfter = $seconds;
            // Save cache item
            cache()->cacheItem($cacheKey, 1);

            // Second cache expiration check
            cache()->expireAfter = $seconds * 2;
            // Save cache item
            cache()->cacheItem($cacheRemainTime, $currentTimePlus);

        } else{

            $requests = cache()->cacheItem($cacheKey);

            $timeLost = cache()->cacheItem($cacheRemainTime);

            if ($currentTime > $timeLost) {

                // as first time request
                $currentTimePlus = date("Y-m-d H:i:s", strtotime("+".$seconds." seconds"));

                // First cache expiration check
                cache()->expireAfter = $seconds;
                // Save cache item
                cache()->cacheItem($cacheKey, 1);

                // Second cache expiration check
                cache()->expireAfter = $seconds * 2;
                // Save cache item
                cache()->cacheItem($cacheRemainTime, $currentTimePlus);
            
            } else {

                // First cache expiration check
                cache()->expireAfter = $seconds;
                // Save cache item
                cache()->cacheItem($cacheKey, $requests + 1);
            
            }

            $requests = cache()->cacheItem($cacheKey);

            if ($requests > $maxRequests) {

                if (filter_var($request, FILTER_VALIDATE_IP)) {
                    log_message('app', "Too Many Requests for an IPV4 with IP: " . $request);
                } else if (filter_var($request, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                    log_message('app', "Too Many Requests for an IPV6 with IP: " . $request);
                } else {
                    log_message('app', "Too Many Requests for a url with path: " . $request . ' and IP: ' . ip_address() );
                }

                header("HTTP/1.0 429 Too Many Requests");
                exit;
            }

        }

    }

    /**
     * Limit Requests By Url
     *
     * @param string $url
     * @param integer $maxRequests
     * @param integer $seconds
     * @return void
     */
    public static function urlRequests(string $url, $maxRequests = 100, $seconds = 300)
    {
        $request = $url . ip_address();
        static::ipRequests($request, $maxRequests, $seconds);
    }
    
}
