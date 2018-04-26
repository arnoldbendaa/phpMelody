<?php
// +------------------------------------------------------------------------+
// | PHP Melody ( www.phpsugar.com )
// +------------------------------------------------------------------------+
// | PHP Melody IS NOT FREE SOFTWARE
// | If you have downloaded this software from a website other
// | than www.phpsugar.com or if you have received
// | this software from someone who is not a representative of
// | PHPSUGAR, you are involved in an illegal activity.
// | ---
// | In such case, please contact: support@phpsugar.com.
// +------------------------------------------------------------------------+
// | Developed by: PHPSUGAR (www.phpsugar.com) / support@phpsugar.com
// | Copyright: (c) 2004-2013 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

class resize_img
{
  //	holds the image path
  var $image_path = '';
  
  //	the limit of the image width
  var $sizelimit_x = 100;
  
  //	the limit of the image height
  var $sizelimit_y = 100;
  
  //	holds the image resource
  var $image_resource = '';
  
  //	if true it keeps the image proportions when resized
  var $keep_proportions = true;
  
  //	holds the resized image resource
  var $resized_resource = '';
  
  var $gdlib = false;
  
  var $output = 'SAMETYPE'; //	can be JPG, GIF, PNG, or SAMETYPE 
  
  var $error = '';
  
  function __construct()
  {
    if(function_exists('gd_info'))
  	{ 
  		$this->gdlib = true; 
  	}
  }
  
  function resize_image($image_path)
  {
  	//	no GD installed on the server!
    if($this->gdlib === false)
	{
		$this->error = 'GD doesn\'t seem to be present.'; 
		return false; 
	}
    
	//	this is going to get the image width, height, and format
    list($img_width, $img_height, $img_type, $img_attr) = @getimagesize($image_path);

	//	make sure it was loaded correctly
    if(($img_width != 0) || ($img_width != 0))
    {
      switch($img_type)
      {
        case 1:
          //	GIF
          $this->image_resource = @imagecreatefromgif($image_path);
          if($this->output == 'SAMETYPE')
		  { 
		  	$this->output = 'GIF'; 
		  }
          break;
        case 2:
          //	JPG
          $this->image_resource = @imagecreatefromjpeg($image_path);
          if($this->output == 'SAMETYPE')
		  {
		  	$this->output = 'JPG';
		  }
          break;  
        case 3:
          //	PNG
          $this->image_resource = @imagecreatefrompng($image_path);
          if($this->output == 'SAMETYPE')
		  { 
		  	$this->output = 'PNG'; 
		  }
      }
      if($this->image_resource === '')
	  {
	  	$this->error = 'Can\'t read image source. Not an image?';
	  	return false;
	  }
    }
    else
	{ 
		$this->error = 'Error in creating image from source.';
		return false;
	}
    
    if($this->keep_proportions === true)
    {
      if(($img_width-$this->sizelimit_x) > ($img_height-$this->sizelimit_y))
      { 
	  	//	if the width of the img is greater than the size limit we scale by width
        $scalex = ($this->sizelimit_x / $img_width);
        $scaley = $scalex;
      }
      else 
      {
	  	//	if the height of the img is greater than the size limit we scale by height
        $scalex = ($this->sizelimit_y / $img_height);
        $scaley = $scalex;
      }

    }
    else 
    {
	  //	just make the image fit the image size limit
      $scalex = ($this->sizelimit_x / $img_width);
      $scaley = ($this->sizelimit_y / $img_height);
      
	  //	don't make it so it streches the image
      if($scalex > 1){ $scalex = 1; }
      if($scaley > 1){ $scaley = 1; }
    }
    
    $new_width = $img_width * $scalex;
    $new_height = $img_height * $scaley;
    
    $this->resized_resource = @imagecreatetruecolor($new_width, $new_height);
    //	creates an image resource, with the width and height of the size limits (or new resized proportion)
   
    if(function_exists('imageantialias')){
		@imageantialias($this->resized_resource, true); 
	}
    @imagecopyresampled($this->resized_resource, $this->image_resource, 0, 0, 0, 0, $new_width, $new_height, $img_width, $img_height);
	
	//	destory old image resource    
    @imagedestroy($this->image_resource);

    return true;
  }
  
  function save_resizedimage($path, $name)
  {
	//	Force image type JPG
	$this->output = 'JPG';
    switch(strtoupper($this->output))
    {
      case 'GIF':
        //	GIF
        @imagegif($this->resized_resource, $path . $name . '.gif');
        break;
      case 'JPG':
        //	JPG
        @imagejpeg($this->resized_resource, $path . $name . '.jpg');
        break;  
      case 'PNG':
        //	PNG
        @imagepng($this->resized_resource, $path . $name . '.png');
    }
  }
  
  function output_resizedimage()
  {
    $the_time = time();
    header('Last-Modified: ' . date('D, d M Y H:i:s', $the_time) . ' GMT'); 
    header('Cache-Control: public');

    switch(strtoupper($this->output))
    {
      case 'GIF':
        //	GIF
        header('Content-type: image/gif');
        @imagegif($this->resized_resource);
        break;
      case 'JPG':
        //	JPG
        header('Content-type: image/jpg');
        @imagejpeg($this->resized_resource);
        break;  
      case 'PNG':
        //	PNG
        header('Content-type: image/png');
        @imagepng($this->resized_resource);
    }
  }
  
  function destroy_resizedimage()
  {
    @imagedestroy($this->resized_resource);
    @imagedestroy($this->image_resource);
  }
}

?>