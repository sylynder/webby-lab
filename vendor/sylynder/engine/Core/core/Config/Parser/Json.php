<?php

namespace Base\Config\Parser;

use \Base\Exceptions\ParseException;

/**
 * JSON parser
 */
class Json implements ParserInterface
{
    /**
     * {@inheritDoc}
     * Parses an JSON file as an array
     *
     * @throws ParseException If there is an error parsing the JSON file
     */
    public function parseFile($filename)
    {
        $data = json_decode(file_get_contents($filename), true);

        return (array)$this->parse($data, $filename);
    }

    /**
     * {@inheritDoc}
     * Parses an JSON string as an array
     *
     * @throws ParseException If there is an error parsing the JSON string
     */
    public function parseString($config)
    {
        $data = json_decode($config, true);

        return (array)$this->parse($data);
    }

    /**
     * Completes parsing of JSON data
     *
     * @param  array  $data
     * @param  string $filename
     * @return array|null
     *
     * @throws \Base\Exceptions\ParseException If there is an error parsing the JSON data
     */
    protected function parse($data = null, $filename = null)
    {
        if (json_last_error() !== JSON_ERROR_NONE) {
            $error_message  = 'Syntax error';
            if (function_exists('json_last_error_msg')) {
                $error_message = json_last_error_msg();
            }

            $error = [
                'message' => $error_message,
                'type'    => json_last_error(),
                'file'    => $filename,
            ];

            throw new ParseException($error);
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSupportedExtensions()
    {
        return ['json'];
    }
}
