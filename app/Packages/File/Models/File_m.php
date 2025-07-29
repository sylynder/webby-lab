<?php

use Base\Models\EasyModel;

require_once PACKAGEPATH . 'file/libraries/ImageResize.php';

class File_m extends EasyModel 
{

	public function __construct() 
	{
		parent::__construct();
		use_helper('file');

	}

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

	//checks if upload file is empty
	function isUploadEmpty($file)
	{
		if ( ! empty($file['name'])) {
			return false;
		}

		// return (!empty($file['name'])) ? false : true;
		
		return true;
	}
	
	function uploadImage($image, $folder, $name = null)
	{
		
		if (!empty($image)) {

			$temp_file = $image['tmp_name'];    
			
			$filepath = realpath($folder);

			if ($name !== null) {
				$image_name = $this->setFileName($image, $name);
				$target_file =  $filepath . $image['name'];
				$target_file =  $filepath . DS .$image_name;
				$this->uploadFile($temp_file, $target_file);
				return $image_name;
			} else {
				$image_name = $this->setFileNameRandom($image, 30);
				$target_file =  $filepath . DS .$image_name;
				$this->uploadFile($temp_file, $target_file);
				return $image_name;
			}

		}
		
		return false;
	}

	function uploadCoverImage($image, $folder, $user_id = null)
	{
		$storeFolder = $folder;   //store folder name here
		//Configurations to resize image
		$config['image_path'] = $image_path;
		$config['new_image_name'] = $image_name;
		$config['new_image_path'] = $file_path;
		$config['width'] = 400;
		$config['height'] = 167;
		
		if (!empty($image)) {
			$temp_file = $image['tmp_name'];          //3  
			$file_path = realpath($folder);  //4
			if ($user_id != null) {
				$image_name = $this->setFileNameWithUserId($image, $user_id);
				$target_filename =  $file_path . DS . $image_name;
				$image_path = $this->uploadFile($temp_file, $target_filename); //6
				$this->resizeImage($config); //resized image
				return $image_name; //return the uploaded image name
			} else {
				$image_name = $this->setFileNameRandom($image);
				$target_filename =  $file_path . DS . $image_name;
				$image_path = $this->uploadFile($temp_file, $target_filename); 
				$this->resizeImage($config); //resized image
				return $image_name; //return the uploaded image name
			}
		} else {
			return false;
		}
	}

	function uploadResizeCoverImage($width, $height, $image, $folder, $user_id = null)
	{
		$storeFolder = $folder;   //store folder name here
		//Configurations to resize image
		$config['image_path'] = $image_path;
		$config['new_image_name'] = $image_name;
		$config['new_image_path'] = $file_path;
		$config['width'] = $width;
		$config['height'] = $height;
		
		
		if (!empty($image)) {
			$temp_file = $image['tmp_name'];          //3  
			$file_path = realpath($folder); 
			if ($user_id != null) {
				$image_name = $this->setFileNameWithUserId($image, $user_id);
				$target_filename =  $file_path . DS . $image_name;
				$image_path = $this->uploadFile($temp_file, $target_filename); 
				$this->resizeImage($config); //resized image
				return $image_name; //return the uploaded image name
			} else {
				$image_name = $this->setFileNameRandom($image);
				$target_filename =  $file_path . DS . $image_name;
				$image_path = $this->uploadFile($temp_file, $target_filename);
				$this->resizeImage($config); //resized image
				return $image_name; //return the uploaded image name
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
			$file_path = realpath($folder); //4
			$image_name = $this->getFileName($image['name']) .'.'. $this->getFileExtension($image['name']);
			$target_filename =  $file_path . DS . $image_name;
			$image_path = $this->uploadFile($temp_file, $target_filename); //6
			return $image_name; //return the uploaded image name
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
    
    function resizeImageUpload($width, $height, $file, $file_path, $name = null) {
        
        $file_size = $file['size'] / 1024;
		
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
        
        $image_name = $this->getFileName($name) .'.'. $this->getFileExtension($name);
        
        if ($this->getFileExtension($name) === 'jpeg') {
        	$image_name = $this->getFileName($name) .'.'. $jpg;
        }

        $image_path = $file_path . DS . $image_name;
        
        /* read binary data from image file */
        $image_string = file_get_contents($file['tmp_name']);
        /* create image from string */
        $image = imagecreatefromstring($image_string);
        $temp_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($temp_image, $image, 0, 0, $image_ratio, 0, $width, $height, $image_weight, $image_height);
        /* Save image */
        switch ($file['type']) {
            case 'image/jpeg':
                imagejpeg($temp_image, $image_path, 100);
                break;
            case 'image/png':
                imagepng($temp_image, $image_path, 0);
                break;
            case 'image/gif':
                imagegif($temp_image, $image_path);
                break;
            default:
                exit;
                break;
        }
        return $image_name;
        /* cleanup memory */
        imagedestroy($image);
        imagedestroy($temp_image);
    }

    function cropImageUpload($width, $height, $file, $file_path, $name = null) {
        
        $file_size = $file['size'] / 1024;
		
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
        
        $image_name = $this->getFileName($name) .'.'. $this->getFileExtension($name);
        $image_path = $file_path . DS . $image_name;
        
        /* read binary data from image file */
        $image_string = file_get_contents($file['tmp_name']);
        /* create image from string */
        $image = imagecreatefromstring($image_string);
        $temp_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($temp_image, $image, 0, 0, $image_ratio, 0, $width, $height, $image_weight, $image_height);
        /* Save image */
        switch ($file['type']) {
            case 'image/jpeg':
                imagejpeg($temp_image, $image_path, 100);
                break;
            case 'image/png':
                imagepng($temp_image, $image_path, 0);
                break;
            case 'image/gif':
                imagegif($temp_image, $image_path);
                break;
            default:
                exit;
                break;
        }
        return $image_name;
        /* cleanup memory */
        imagedestroy($image);
        imagedestroy($temp_image);
    }
	
	function showResizedImage($image, $width, $height)
    {
		// Path to image thumbnail in your root
        $dir = ASSETS . 'trades/images/news_cover_images/';
        $thumb_dir = ASSETS . 'trades/images/news_cover_images/thumbs/';
        //$dir = ASSET . 'trades/images/news_cover_images/thumbs/';
        $url = base_url() . 'trades/images/news_cover_images/';
		
        // Get the CodeIgniter super object
        $ci = &get_instance();

        // get src file's extension and file name
        $extension = pathinfo($image, PATHINFO_EXTENSION);
        $filename = pathinfo($image, PATHINFO_FILENAME);
        $image_org = $dir . $filename . "." . $extension;
        $image_thumb = $thumb_dir . $filename . "-" . $height . '_' . $width . "." . $extension;
        $image_returned = $url . $filename . "-" . $height . '_' . $width . "." . $extension;

        if ( ! file_exists($image_thumb)) {
            // LOAD LIBRARY
            $ci->load->library('image_lib');
            // CONFIGURE IMAGE LIBRARY
            $config['source_image'] = $image_org;
            $config['new_image'] = $image_thumb;
            $config['width'] = $width;
            $config['height'] = $height;
			
            $ci->image_lib->initialize($config);
            $ci->image_lib->resize();
            $ci->image_lib->clear();
        }
        return $image_thumb;
	}

	public function uploadFile($temp_file, $target_file)
	{
		move_uploaded_file($temp_file, $target_file);
		
		return $target_file;
	}

	public function removeFile($file_path)
	{
		
		if( ! empty($file_path)) {
			return false;
		}

		if (unlink($file_path)) {
			return true;
		} else {
			return false;
		}
	}

	//New features end here //

	//Old features
	public function createFolder($root, $folder_name)
	{

		$folder = $root . $folder_name;

		//create the folder if it's not already exists
		if( ! is_dir($folder)) 
		{
			mkdir($folder, 0777, true);
		} 	
		//Check if folder has been created and return true
		if(is_dir($folder)) 
		{
			return true;
		} 
		else
		{
			return false;
		}
	}

}
