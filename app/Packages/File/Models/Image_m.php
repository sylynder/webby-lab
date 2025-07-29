<?php

use Base\Models\EasyModel;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 
 * 
 */
// require_once PACKAGEPATH . 'File/Libraries/ImageResize.php';

use App\Packages\File\Libraries\ImageResize;

class Image_m extends EasyModel 
{

	public function __construct() 
	{
		parent::__construct();
		$this->load->helper('file');

	}

	public function showImage($src, $width, $height, $name = null) {
        //Prepare Image For Resize;
		$config = [];
		$image = new ImageResize($src);//Pass the image path here
		//First Image Resize
		$image->resize($width, $height);//Crop the image to desirable size
		$image->save($config['new_image_path'] . $config['new_image_name']); //Copy the resized image to destination path
    }
}