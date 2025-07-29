<?php

namespace App\Packages\File\Libraries;

use App\Packages\File\Libraries\ImageResize;

class Upload
{
	public function __construct() 
	{
		use_helper('File');
	}

	/**
	 * Set File
	 *
	 * @param string $file
	 * @return array|string
	 */
	function setFile($file)
	{
		return $_FILES[$file];
	}

	function setFileNameWithUserId($file, $user_id)
	{
		$extension = $this->getFileExtension($file['name']);
		return $this->fileId($user_id, $file['name'], 13) . '.' . $extension;
	}

	function setFileNameRandom($file, $length=null)
	{
		$extension = $this->getFileExtension($file['name']);
        
        if ($length == null) {
            $length = 20;
        }

		return $this->fileIdRandom($file['name'], $length) . '.' . $extension;
	}

	function setFileName($file, $name)
	{
		$extension = $this->getFileExtension($file['name']);
		return $name . '.' . $extension;
	}

	//Get user id and file name to create a new file id
	function fileId($user_id, $file_name, $length)
	{
		$hash_data = hash_algo('md5', $file_name . $user_id);
		return $user_id ."_". substr($hash_data, 0, $length);	
	}

	//Get file name to create a new file id
	function fileIdRandom($file_name, $length)
	{
		$hash_data = hash_algo('md5', $file_name . unique_code($length));
		return $file_id = substr($hash_data, 0, $length);	
	}

	//accepts $_FILE['file']['name] to extract extension eg. .txt, .png etc
	function getFileExtension($file)
	{
		return pathinfo($file, PATHINFO_EXTENSION);
	}

	function getFileType($file)
	{
		return $this->getFileExtension($file['name']);
	}

	//accepts $_FILE['file']['name] to extract extension eg. .txt, .png etc
	function getFileName($file)
	{
		return pathinfo($file, PATHINFO_FILENAME);
	}

	/**
	 * Checks if upload file is empty
	 *
	 * @param array $file
	 * @return boolean
	 */
	function hasFile($file)
	{
		if ( ! empty($file['name'])) {
			return true;
		}

		return false;
	}
	
	function uploadImage($image, $path, $name = null)
	{

		if (empty($image)) {
			return '';
		}

		$tempfile = $image['tmp_name'];    
		
		$filepath = realpath($path);
		
		$imagename = '';
		
		if ($name !== null) {
			$imagename = $this->setFileName($image, $name);
			$targetfile =  $filepath . $image['name'];
			$targetfile =  $filepath . DS . $imagename;
			$this->uploadFile($tempfile, $targetfile);
		} 
		
		if ($name === null) {
			$imagename = $this->setFileNameRandom($image, 20);
			$targetfile =  $filepath . DS . $imagename;
			$this->uploadFile($tempfile, $targetfile);
		}

		return $imagename;
		
	}

	function uploadDocument($file, $folder, $name = null)
	{
		
		if (empty($file)) {
			return '';
		}

		$tempfile = $file['tmp_name'];    
			
		$filepath = realpath($folder);
		$filename = '';

		if ($name !== null) {
			$filename = $this->setFileName($file, $name);
			$targetfile =  $filepath . $file['name'];
			$targetfile =  $filepath . DS .$filename;
			$this->uploadFile($tempfile, $targetfile);
		}

		if ($name === null) {
			$filename = $this->setFileNameRandom($file, 20);
			$targetfile =  $filepath . DS . $filename;
			$this->uploadFile($tempfile, $targetfile);
		}

		return $filename;
		
	}

	function uploadResizeCoverImage($width, $height, $image, $folder, $user_id = null)
	{
		$storeFolder = $folder;   //store folder name here
		//Configurations to resize image
		$config['image_path'] = $image; //$imagepath;
		$config['new_image_name'] = $image; //$imagename;
		$config['new_image_path'] = $image; //$filepath;
		$config['width'] = $width;
		$config['height'] = $height;
		
		
		if (!empty($image)) {
			$temp_file = $image['tmp_name'];          //3  
			$filepath = realpath($folder); 
			if ($user_id != null) {
				$imagename = $this->setFileNameWithUserId($image, $user_id);
				$target_filename =  $filepath . DS . $imagename;
				$imagepath = $this->uploadFile($temp_file, $target_filename); 
				$this->resizeImage($config); //resized image
				return $imagename; //return the uploaded image name
			} else {
				$imagename = $this->setFileNameRandom($image);
				$target_filename =  $filepath . DS . $imagename;
				$imagepath = $this->uploadFile($temp_file, $target_filename);
				$this->resizeImage($config); //resized image
				return $imagename; //return the uploaded image name
			}
		} else {
			return false;
		}
	}

	function uploadPageImage($image, $folder)
	{
		$storeFolder = $folder;   //store folder name here
		//echo $targetPath = realpath(APP_DATA) . $ds . $storeFolder . $ds;  
		if (!empty($image)) {
			$temp_file = $image['tmp_name'];          //3  
			$filepath = realpath($folder); //4
			$imagename = $this->getFileName($image['name']) .'.'. $this->getFileExtension($image['name']);
			$target_filename =  $filepath . DS . $imagename;
			$imagepath = $this->uploadFile($temp_file, $target_filename); //6
			return $imagename; //return the uploaded image name
		} else {
			return false;
		}
	}

	function resizeImage(array $config)
	{
		//Prepare Image For Resize;
		$image = new ImageResize($config['image_path']);//Pass the image path here
		//First Image Resize
		$image->resize($config['width'], $config['height']);//Crop the image to desirable size
		$image->save($config['new_image_path'] . $config['new_image_name']); //Copy the resized image to destination path
		return true;
	}
    
    function resizeImageUpload($width, $height, $file, $filepath, $name = null) {
        
        $filesize = $file['size'] / 1024;
		
		if ( ! empty($file['tmp_name'])) {
			list($image_weight, $image_height) = getimagesize($file['tmp_name']);
		} else {
			return false;
		}
        
        @$ratio = max($width / $image_weight, $height / $image_height);
        $image_height = ceil($height / $ratio);
        $image_ratio = ($image_weight - $width / $ratio) / 2;
        $image_weight = ceil($width / $ratio);
		
        if ( ! is_null($name)) {
            $name = $this->setFileName($file, $name);
        } else {
            $name = $this->setFileNameRandom($file, 30);
        }

        $jpg = 'jpg';
        
        $imagename = $this->getFileName($name) .'.'. $this->getFileExtension($name);
        
        if ($this->getFileExtension($name) === 'jpeg') {
        	$imagename = $this->getFileName($name) .'.'. $jpg;
        }

        $imagepath = $filepath . DS . $imagename;
        
        /* read binary data from image file */
        $image_string = file_get_contents($file['tmp_name']);
        
		/* create image from string */
        $image = imagecreatefromstring($image_string);
        $tempimage = imagecreatetruecolor($width, $height);
        imagecopyresampled($tempimage, $image, 0, 0, $image_ratio, 0, $width, $height, $image_weight, $image_height);
        
		/* Save image */
        switch ($file['type']) {
            case 'image/jpeg':
                imagejpeg($tempimage, $imagepath, 100);
                break;
            case 'image/png':
                imagepng($tempimage, $imagepath, 0);
                break;
            case 'image/gif':
                imagegif($tempimage, $imagepath);
                break;
            default:
                exit;
                break;
        }
        return $imagename;

        /* cleanup memory */
        imagedestroy($image);
        imagedestroy($tempimage);
    }

    function cropImageUpload($width, $height, $file, $filepath, $name = null) {
        
        $filesize = $file['size'] / 1024;
		
		if ( ! empty($file['tmp_name'])) {
			list($image_weight, $image_height) = getimagesize($file['tmp_name']);
		} else {
			return false;
		}
        
        @$ratio = max($width / $image_weight, $height / $image_height);
        $image_height = ceil($height / $ratio);
        $image_ratio = ($image_weight - $width / $ratio) / 2;
        $image_weight = ceil($width / $ratio);
		
        if ( ! is_null($name)) {
            $name = $this->setFileName($file, $name);
        } else {
            $name = $this->setFileNameRandom($file, 30);
        }
        
        $imagename = $this->getFileName($name) .'.'. $this->getFileExtension($name);
        $imagepath = $filepath . DS . $imagename;
        
        /* read binary data from image file */
        $image_string = file_get_contents($file['tmp_name']);
        
		/* create image from string */
        $image = imagecreatefromstring($image_string);
        $tempimage = imagecreatetruecolor($width, $height);
        imagecopyresampled($tempimage, $image, 0, 0, $image_ratio, 0, $width, $height, $image_weight, $image_height);
        
		/* Save image */
        switch ($file['type']) {
            case 'image/jpeg':
                imagejpeg($tempimage, $imagepath, 100);
                break;
            case 'image/png':
                imagepng($tempimage, $imagepath, 0);
                break;
            case 'image/gif':
                imagegif($tempimage, $imagepath);
                break;
            default:
                exit;
                break;
        }
        return $imagename;
        /* cleanup memory */
        imagedestroy($image);
        imagedestroy($tempimage);
    }
	
	function showResizedImage($image, $width, $height)
    {
		// Path to image thumbnail in your root
        $dir = ASSETS . 'trades/images/news_cover_images/';
        $thumb_dir = ASSETS . 'trades/images/news_cover_images/thumbs/';
        // $dir = ASSET . 'trades/images/news_cover_images/thumbs/';
        $url = base_url() . 'trades/images/news_cover_images/';
		
        // Get the CodeIgniter super object
        $ci = &get_instance();

        // get src file's extension and file name
        $extension = pathinfo($image, PATHINFO_EXTENSION);
        $filename = pathinfo($image, PATHINFO_FILENAME);
        $imageorg = $dir . $filename . "." . $extension;
        $imageThumb = $thumb_dir . $filename . "-" . $height . '_' . $width . "." . $extension;
        $image_returned = $url . $filename . "-" . $height . '_' . $width . "." . $extension;

        if ( ! file_exists($imageThumb)) {
            // LOAD LIBRARY
            $ci->load->library('image_lib');
            // CONFIGURE IMAGE LIBRARY
            $config['source_image'] = $imageorg;
            $config['new_image'] = $imageThumb;
            $config['width'] = $width;
            $config['height'] = $height;
			
            $ci->image_lib->initialize($config);
            $ci->image_lib->resize();
            $ci->image_lib->clear();
        }
        return $imageThumb;
	}

	public function uploadFile($tempfile, $targetfile)
	{
		move_uploaded_file($tempfile, $targetfile);
		
		return $targetfile;
	}

	public function removeFile($filepath)
	{
		
		if( ! empty($filepath)) {
			return false;
		}

		if (unlink($filepath)) {
			return true;
		} else {
			return false;
		}
	}

	// New features end here //

	// Old features
	public function createFolder($root, $foldername)
	{

		$folder = $root . $foldername;

		// create the folder if it's not already exists
		if ( ! is_dir($folder)) {
			mkdir($folder, 0777, true);
		}

		// Check if folder has been created and return true
		if (is_dir($folder))  {
			return true;
		}  else {
			return false;
		}
	}

}
