<?php

/**
 *	Image Class to do simple manipulations
 */

namespace App\Packages\File\Libraries;

class Image
{

    public $destinationFolder = '';
    public $filterFolder = '';
    private $watermark;
    private $file;
    private $image;
    private $width;
    private $height;
    private $extension;
    private $filesize;

    const CROPTOP = 1;
    const CROPCENTRE = 2;
    const CROPCENTER = 2;
    const CROPBOTTOM = 3;
    const CROPLEFT = 4;
    const CROPRIGHT = 5;

    public $quality_jpg = 85;
    public $quality_png = 6;
    public $quality_truecolor = true;

    public $interlace = 1;

    public $source_type;

    protected $source_image;

    protected $original_w;
    protected $original_h;

    protected $dest_x = 0;
    protected $dest_y = 0;

    protected $source_x;
    protected $source_y;

    protected $dest_w;
    protected $dest_h;

    protected $source_w;
    protected $source_h;


    public function __construct()
    {
        $this->watermark = new \stdClass;
        $this->watermark->opacity = 60;
        $this->watermark->position = 'bottomright';
        $this->watermark->padding = 20;
        $this->watermark->size = 200;
        $this->watermark->x = 0;
        $this->watermark->y = 0;
        $this->watermark->image = $this->destinationFolder . 'watermark.png';
    }

    /**
     * Function to load images using absolute path
     * @param string filename
     */
    public function load(string $filename)
    {
        $this->file = $filename;

        list($width, $height) = getimagesize($filename);
        $this->width = $width;
        $this->height = $height;

        //get image extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $extension = '';

        switch ($ext) {
            case 'jpg':
                $extension = 'jpeg';
                break;

            case 'jpeg':
                $extension = 'jpeg';
                break;

            case 'png':
                $extension = 'png';
                break;

            case 'gif':
                $extension = 'gif';
                break;
            case 'webp':
                $extension = 'webp';
                break;
        }

        $this->extension = $extension;
        
        //get filename without extension
        $pieces = explode('/', $filename);
        $pcs = explode('.', $pieces[1]);
        $file = $pcs[0];
        $this->filename = $file;
        //load filesize
        $this->filesize = filesize($filename);

        //create image instance and store it
        $imgcreate = 'imagecreatefrom' . $extension;
dd($imgcreate);
        $img = $imgcreate($filename);
        //store the image instance $this object
        $this->image = $img;

        return $this;
    }

    public function getSourceWidth()
    {
        return $this->width;
    }

    public function getSourceHeight()
    {
        return $this->height;
    }

    /**
     * Resizes image according to the given height (width proportional)
     *
     * @param integer $height
     * @param boolean $allow_enlarge
     * @return \static
     */
    public function resizeToHeight($height, $allow_enlarge = false)
    {
        $ratio = $height / $this->getSourceHeight();
        $width = $this->getSourceWidth() * $ratio;

        $this->resize($height, $width, $allow_enlarge);

        return $this;
    }

    /**
     * Resizes image according to the given width (height proportional)
     *
     * @param integer $width
     * @param boolean $allow_enlarge
     * @return \static
     */
    public function resizeToWidth($width, $allow_enlarge = false)
    {
        $ratio  = $width / $this->getSourceWidth();
        $height = $this->getSourceHeight() * $ratio;

        $this->resize($height, $width, $allow_enlarge);

        return $this;
    }

    /**
     * Resizes image to best fit inside the given dimensions
     *
     * @param integer $max_width
     * @param integer $max_height
     * @param boolean $allow_enlarge
     * @return \static
     */
    public function resizeToBestFit($max_width, $max_height, $allow_enlarge = false)
    {
        if ($this->getSourceWidth() <= $max_width && $this->getSourceHeight() <= $max_height && $allow_enlarge === false) {
            return $this;
        }

        $ratio  = $this->getSourceHeight() / $this->getSourceWidth();
        $width = $max_width;
        $height = $width * $ratio;

        if ($height > $max_height) {
            $height = $max_height;
            $width = $height / $ratio;
        }

        return $this->resize($height, $width, $allow_enlarge);
    }

    /**
     * Gets width of the destination image
     *
     * @return integer
     */
    public function getDestWidth()
    {
        return $this->dest_w;
    }

    /**
     * Gets height of the destination image
     * @return integer
     */
    public function getDestHeight()
    {
        return $this->dest_h;
    }

    /**
     * Gets crop position (X or Y) according to the given position
     *
     * @param integer $expectedSize
     * @param integer $position
     * @return integer
     */
    protected function getCropPosition($expectedSize, $position = self::CROPCENTER)
    {
        $size = 0;
        switch ($position) {
            case self::CROPBOTTOM:
            case self::CROPRIGHT:
                $size = $expectedSize;
                break;
            case self::CROPCENTER:
            case self::CROPCENTRE:
                $size = $expectedSize / 2;
                break;
        }
        return $size;
    }

    /** 
     * Function to resize the image and by default 
     * reduces the size by 50%
     * detects %,PX,numeric values automatically 
     * @param int $size
     * @param mixed $on
     */
    public function resize(int $size = null, $on = null, $allow_enlarge = false)
    {
        // get the resize value 
        if ($size == null) {
            $size = '50%';
        }

        // if second parameter is not defined take it as width
        if ($on == null) {
            $on = 'width';
        }

        // get the constraint new width and height
        if (!$this->percent($on)) {

            if ($on != 'square') {
                $percent = $this->percent($size, $this->$on);
                $newwidth = $this->width * $percent;
                $newheight = $this->height * $percent;
            } else {
                $percent = $this->percent($size, $this->width);
                $newwidth = $newheight = $this->width * $percent;
            }
        } else {
            $newwidth = $this->width * $this->percent($size, $this->width);
            $newheight = $this->height * $this->percent($on, $this->height);
        }

        // create the new image container
        $thumb = imagecreatetruecolor($newwidth, $newheight);

        // if its png make the background transparent
        if ($this->extension == 'png') {
            imagesavealpha($thumb, true);
            imagealphablending($thumb, false);
            $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
            imagefilledrectangle($thumb, 0, 0, $newwidth, $newheight, $transparent);
        }

        // Resize to contianer
        imagecopyresized($thumb, $this->image, 0, 0, 0, 0, $newwidth, $newheight, $this->width, $this->height);

        $this->source_x = 0;
        $this->source_y = 0;

        $this->dest_w = $newwidth;
        $this->dest_h = $newheight;

        $this->source_w = $this->getSourceWidth();
        $this->source_h = $this->getSourceHeight();

        // update the current object
        $this->width = $newwidth;
        $this->height = $newheight;
        $this->image = $thumb;
        return $this;
    }

    /** 
     * Function to crop images and it takes
     * source coordinates and destination dimensions	
     */
    public function crop(
        $srcWidth,
        $srcHeight,
        $destinationWidth,
        $destinationHeight
    ) {

        $destinationImage = ImageCreateTrueColor($destinationWidth, $destinationHeight);

        imagecopyresampled(
            $destinationImage,
            $this->image,
            0,
            0,
            $srcWidth,
            $srcHeight,
            $destinationWidth,
            $destinationHeight,
            $destinationWidth,
            $destinationHeight
        );

        $this->image = $destinationImage;
        $this->width = $destinationWidth;
        $this->height = $destinationHeight;

        return $this;
    }

    /**
     * Crops image according to the given width, height and crop position
     *
     * @param integer $width
     * @param integer $height
     * @param boolean $allow_enlarge
     * @param integer $position
     * @return \static
     */
    public function freecrop($width, $height, $allow_enlarge = false, $position = self::CROPCENTER)
    {
        if (!$allow_enlarge) {
            // this logic is slightly different to resize(),
            // it will only reset dimensions to the original
            // if that particular dimenstion is larger

            if ($width > $this->getSourceWidth()) {
                $width  = $this->getSourceWidth();
            }

            if ($height > $this->getSourceHeight()) {
                $height = $this->getSourceHeight();
            }
        }

        $ratio_source = $this->getSourceWidth() / $this->getSourceHeight();
        $ratio_dest = $width / $height;

        if ($ratio_dest < $ratio_source) {
            $this->resizeToHeight($height, $allow_enlarge);

            $excess_width = ($this->getDestWidth() - $width) / $this->getDestWidth() * $this->getSourceWidth();

            $this->source_w = $this->getSourceWidth() - $excess_width;
            $this->source_x = $this->getCropPosition($excess_width, $position);

            $this->dest_w = $width;
        } else {
            $this->resizeToWidth($width, $allow_enlarge);

            $excess_height = ($this->getDestHeight() - $height) / $this->getDestHeight() * $this->getSourceHeight();

            $this->source_h = $this->getSourceHeight() - $excess_height;
            $this->source_y = $this->getCropPosition($excess_height, $position);

            $this->dest_h = $height;
        }

        return $this;
    }

    /**
     * Function to display the image to browser
     */
    public function show()
    {
        $type = $this->extension;
        $imgOutput = 'image' . $type;
        header("Content-Type: image/$type");
        $imgOutput($this->image);
        return $this;
    }

    /**
     * Function to save the image to disk
     */
    public function save($path = null)
    {

        $file = $this->name;

        if ($path === null) {
            $path = $this->destinationFolder . $file . '-new-' . substr(md5(rand()), 0, 3) . '.' . $this->extension;
            $newFile = $path;
        }

        $imgOutput = 'image' . $this->extension;
        $imgOutput($this->image, $path);
        $this->savepath = $path;
        return $this;
    }

    /**
     * Calculates the ratio of the user given value
     * detects the value as either % or PX or numeric
     * returns 0 if its not valid
     * $width is second parameter by defualt taken as width of image 
     */
    public function percent($str, int $width = null)
    {
        if ($width === null) {
            $width = $this->width;
        }

        $str = strtoupper($str);

        if (substr($str, -1) === '%') {
            return (substr($str, 0, strlen($str) - 1) / 100);
        }

        if (substr($str, -2) === 'px') {
            return (substr($str, 0, strlen($str) - 2) / $width);
        }

        if (is_numeric($str)) {
            return ($str / $width);
        }

        return 0;
    }
}
