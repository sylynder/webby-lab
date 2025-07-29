<?php

use App\Packages\File\Libraries\Image;
use Base\Controllers\WebController;

class RenderImage extends WebController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
    }

    private function renderImage(string $image, int $width = 200, int $height = 200)
    {

        if (empty($image)) {
            return;
        }

        if ($width === null || $width < 20) {
            $width = 200;
        }

        if ($height === null || $height < 20) {
            $height = 200;
        }

        $path = pathinfo($image);

        $image = new Image();

        //get the image source file
        $imageFile = $path['dirname'] . DS . $path['basename'];

        $image->load($imageFile) //load image
            ->resize($width, $height) //resizes the image
            ->show(); //display the image
    }

    private function cropImage(string $image, int $width = 200, int $height = 200)
    {
        if (empty($image)) {
            return;
        }

        if ($width === null || $width < 20) {
            $width = 200;
        }

        if ($height === null || $height < 20) {
            $height = 200;
        }

        $path = pathinfo($image);

        $image = new Image();

        //get the image source file
        $imageFile = $path['dirname'] . DS . $path['basename'];

        $image->load($imageFile) //load image
            ->freecrop($width, $height) //resizes the image
            ->show(); //display the image
    }

    private function cropImageTest(string $image, int $width = 200, int $height = 200)
    {

        if (empty($image)) {
            return;
        }

        if ($width === null || $width < 20) {
            $width = 200;
        }

        if ($height === null || $height < 20) {
            $height = 200;
        }

        $path = pathinfo($image);

        $image = new Image();

        // get the image source file
        $imageFile = $path['dirname'] . DS . $path['basename'];

        $dimensions = $loaded = $image->load($imageFile); //load image 

        $image = imagecreatefromjpeg($_GET['src']);
        $filename = $imageFile;

        $thumb_width = 200;
        $thumb_height = 150;

        $width = imagesx($image);
        $height = imagesy($image);

        $original_aspect = $width / $height;
        $thumb_aspect = $thumb_width / $thumb_height;

        if (
            $original_aspect >= $thumb_aspect
        ) {
            // If image is wider than thumbnail (in aspect ratio sense)
            $new_height = $thumb_height;
            $new_width = $width / ($height / $thumb_height);
        } else {
            // If the thumbnail is wider than the image
            $new_width = $thumb_width;
            $new_height = $height / ($width / $thumb_width);
        }

        $thumb = imagecreatetruecolor($thumb_width, $thumb_height);

        // Resize and crop
        imagecopyresampled(
            $thumb,
            $image,
            0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
            0 - ($new_height - $thumb_height) / 2, // Center the image vertically
            0,
            0,
            $new_width,
            $new_height,
            $width,
            $height
        );

        imagejpeg($thumb, $filename, 80);
    }

    public function crop_image($image, int $width = 200, int $height = 200)
    {
        use_helper('File.Image');

        $image = get_image($image);

        $this->cropImage(
            $image,
            $width,
            $height
        );
    }

    public function app_image($image, int $width = 200, int $height = 200)
    {
        use_helper('File.Image');

        $image = get_image($image);

        $this->renderImage($image, $width, $height);
    }
}
