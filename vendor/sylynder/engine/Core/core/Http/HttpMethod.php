<?php

namespace Base\Http;

class HttpMethod 
{
    const HEAD = 'HEAD';
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const PATCH = 'PATCH';
    const OPTIONS = 'OPTIONS';
    const DELETE = 'DELETE';
    
    /**
     * @param string $method
     * @return bool
     */
    public static function isValid($method)
    {
        return in_array($method, [
            self::GET,
            self::POST,
            self::PUT,
            self::PATCH,
            self::DELETE,
            self::OPTIONS
        ]);
    }

    /**
     * Returns an array containing all the HTTP methods supported by the framework.
     *
     * @return array An array of strings, each representing a HTTP method.
     */
    public static function getAll()
    {
        // The array containing all the HTTP methods supported by the framework.
        return [
            self::GET,    // Requests data from the server using the GET method.
            self::POST,   // Submits data to the server using the POST method.
            self::PUT,    // Updates a resource on the server using the PUT method.
            self::PATCH,  // Updates part of a resource on the server using the PATCH method.
            self::DELETE, // Deletes a resource on the server using the DELETE method.
            self::OPTIONS // Asks for the options that the server supports using the OPTIONS method.
        ];
    }
    
}
