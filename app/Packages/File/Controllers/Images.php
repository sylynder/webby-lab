<?php

use App\Packages\File\Libraries\Image;
use Base\Controllers\WebController;

class Images extends WebController 
{

    public function __construct() 
    {
        parent::__construct();
    }

    public function index() {}

    private function renderImage(string $image, int $width=200, int $height=200)
    {
        
        if (empty($image)) { return; } 

        if ($width === null || $width < 20) {
            $width = 200;
        }
        
        if ($height === null || $height < 20) {
            $height = 200;
        }

        $path = pathinfo($image);

        $image = new Image();

        //get the image source file
        $imageFile = $path['dirname'] .DS. $path['basename'];
        
        $image->load($imageFile) //load image
                ->resize($width, $height) //resizes the image
                    ->show(); //display the image
    }

    public function app_image($image, int $width=200, int $height=200)
    {
        use_helper('File.Image');

        $image = get_image($image);

        $this->renderImage($image, $width, $height);
    }

    public function sermon_image($image, int $width=200, int $height=200)
    {
        use_helper('articles.article');

        $image = get_image($image, 'sermon');
 
        $this->renderImage($image, $width, $height);
    }

}