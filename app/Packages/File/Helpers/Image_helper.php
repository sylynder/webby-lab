<?php

// Include PHP Image Magician library
use App\Packages\File\Libraries\Image;

if ( ! function_exists('image')) {

    function image($image, $width = null, $height = null)
    {
        $path = pathinfo($image);

        $image = new Image();

        if ($width === null || $width < 20) {
            $width = 200;
        }
        
        if ($height === null || $height < 20) {
            $height = 200;
        }
        
        //get the image source file
        $imagefile = $path['dirname'] .DS. $path['basename'];

        $image->load($imagefile) //load image
                ->resize($width, $height) //resizes the image
                ->show(); //display the image
    }
}

if ( ! function_exists('article_image')) 
{
    function article_image($article_id, $width, $height)
    {
        return url('article/image/'.$article_id.'/'.$width.'/'.$height);
    }
}

if ( ! function_exists('use_image')) 
{
    function use_image($image, $width = 250, $height = 250)
    {
        return site_url('app/image/'.$image.'/'.$width.'/'.$height);
    }
}

if ( ! function_exists('cdn_image')) 
{
    function cdn_image($image, $width = 250, $height = 250)
    {
        return site_url('cdn/image/'.$image.'/'.$width.'/'.$height);
    }
}

if ( ! function_exists('show_image')) 
{
    function show_image($image = '', $imagepath = '')
    {
        
        $exists = file_exists(image_path($imagepath) . $image);
        
        if ($exists) {
            return site_url(image_path($imagepath). $image);
        } 

        if (!$exists) {
            return site_url(default_image());
        }
    }
    
}

if ( ! function_exists('get_image')) 
{
    function get_image($image = '')
    {
        $image = explode(':', $image);
        
        $imagefile = $image[0];
        $path = $image[1];

        $exists = file_exists(image_path($path) . $imagefile);
       
        if ( $exists) {
            return image_path($path). $imagefile;
        } 

        if ($path === 'avatar') {
            return default_profile_image();
        } 

        return default_image();
    }
    
}

if ( ! function_exists('get_profile_image')) 
{
    function get_profile_image($image = '')
    {

        $exists = file_exists(AVATAR_IMAGE_PATH . $image);
       
        if ( $exists) {
            return AVATAR_IMAGE_PATH . $image;
        } 

        return default_profile_image();
    }
    
}

if( ! function_exists('default_image')) 
{
    function default_image()
    {
        return DEFAULT_IMAGE;
    }
}

if( ! function_exists('default_profile_image')) 
{
    function default_profile_image()
    {
        return DEFAULT_PROFILE_IMAGE;
    }
}

if( ! function_exists('article_image_path')) 
{
    function article_image_path()
    {
        return ARTICLES_IMAGE_PATH;
    }
}

if( ! function_exists('image_path')) 
{

    function image_path($type = '')
    {
        if ($type === 'avatar') {
            return AVATAR_IMAGE_PATH;
        }
        
        if ($type === 'slider') {
            return SLIDERS_IMAGE_PATH;
        }

        if ($type === 'page') {
            return PAGES_IMAGE_PATH;
        }

        if ($type === 'sermon') {
            return SERMONS_IMAGE_PATH;
        } 

        if ($type === 'event') {
            return EVENTS_IMAGE_PATH;
        } 

        if ($type === 'bible-study') {
            return STUDIES_IMAGE_PATH;
        } 

        if ($type === 'article') {
            return ARTICLES_IMAGE_PATH;
        }  

        return SERMONS_IMAGE_PATH;
    }
}

if ( ! function_exists( 'article_cover_image' )) 
{
    function article_cover_image($article_id)
    {
        $image = app()->article->getArticleCoverImage($article_id);

        return (!empty($image) && !empty($article_id))
            ? load_path(article_image_path() . $image)
            : load_path(default_image());
    }
}


