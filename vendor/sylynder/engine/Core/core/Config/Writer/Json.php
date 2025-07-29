<?php

namespace Base\Config\Writer;


use \Base\Config\Writer\AbstractWriter;
use \Base\Exceptions\WriteException;

/**
 * JSON Writer.
 */
class Json extends AbstractWriter
{
    /**
     * 
     * Writes an array to a JSON file.
     * 
     * @throws WriteException
     */
    public function toFile($config, $filename)
    {
        $data = $this->toString($config);
        $success = @file_put_contents($filename, $data.PHP_EOL);
        if ($success === false) {
            throw new WriteException(['file' => $filename]);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     * Writes an array to a JSON string.
     */
    public function toString($config, $pretty = true)
    {
        return json_encode($config, $pretty ? (JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) : 0);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSupportedExtensions()
    {
        return ['json'];
    }
}
