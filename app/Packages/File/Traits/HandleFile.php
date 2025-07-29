<?php

namespace App\Packages\File\Traits;
use App\Packages\File\Libraries\Upload;

trait HandleFile
{
    
    public function moveFile(
        $id, 
        $file, 
        $components = [], 
        Upload $upload = new \stdClass
    ) {
        
        [$path, $modelColumn, $useModel] = $components;

        $filepath = config('paths')[$path];

        if ($file['name'] !== "") {

            $uploaded = $upload->uploadDocument(
                $file,
                $filepath,
                sha1($id . $path)
            );

            $useModel->simpleUpdate(['id' => $id], [$modelColumn => $uploaded]);

            return $uploaded;
        }

        return '';
    }

    public function deleteFile($file, $path = '')
    {
        $filepath = config('paths')[$path];

        $file = $filepath . DS . $file;

        if (file_exists($file)) {
            unlink($file);
            return true;
        }

        return false;
    }
}
