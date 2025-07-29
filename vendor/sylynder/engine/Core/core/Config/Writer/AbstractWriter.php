<?php

namespace Base\Config\Writer;

use \Base\Exceptions\WriteException;

/**
 * Base Writer.
 */
abstract class AbstractWriter implements WriterInterface
{
    /**
     * @throws \Base\Exceptions\WriteException
     */
    public function toFile($config, $filename)
    {
        $contents = $this->toString($config);
        $success = @file_put_contents($filename, $contents);
        if ($success === false) {
            throw new WriteException(['file' => $filename]);
        }

        return $contents;
    }
}
