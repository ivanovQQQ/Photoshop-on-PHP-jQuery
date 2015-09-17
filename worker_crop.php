<?php

session_start();
if(isset($_POST['image'])){
    
    $name = preg_replace("/[\<\>`,\[\]]/", 's', $_POST['image']);
	$ext = strtolower(mb_substr ($name, mb_strrpos($name, '.')+1));
    $x      = intval($_POST['x1']);
    $y      = intval($_POST['y1']);
    $crop_width = intval($_POST['w']);
    $crop_height = intval($_POST['h']);
    $dir = 'crop_images';
    $path = $dir.'/'.microtime(true).'.'.$ext;
    $fullpath = __DIR__ .'/'. $path;
    if (!file_exists ($dir)) mkdir($dir, 0755, true);
    //deleteAllFiles($dir);
    cropImage(__DIR__.'/'.$name, $fullpath, $crop_width, $crop_height, $x, $y);
    
    //session_destroy();
    $_SESSION['cropimg'][] = $path;
    $ses = $_SESSION['cropimg'];
    if(count($ses) > 1){
       unlink($ses[count($ses)-2]);
       //print_r($_SESSION['img']);
    }
                   
    echo "<img src='$path'>";
    
}

function cropImage($sourse, $path, $width, $height, $x, $y){
    $result = false;
    try{
        $img = new Imagick($sourse);
        if($img->getImageMimeType()=='image/gif'){
            
		$new = new Imagick();
        $new->newImage($width, $height, new ImagickPixel('transparent'), 'gif');
		foreach ($img as $key=>$frame) {
            $frame->cropImage($width, $height, $x, $y);  
            if($key==0){
                $new->compositeImage($frame, Imagick::COMPOSITE_DEFAULT, 0, 0);
                $speed = getDelay($sourse);
                $new->setImageDelay($speed);
            }else{
                $new->newImage($width, $height, new ImagickPixel('transparent'), 'gif');
                $new->compositeImage($frame, Imagick::COMPOSITE_DEFAULT, 0, 0);           $new->setImageDelay($speed);
                $new->setImageCompression(Imagick::COMPRESSION_JPEG);
			    $new->setImageCompressionQuality(70);
            }
		}
		// Обратите внимание, writeImages вместо writeImage
		$result=$new->writeImages($path, true);
		
	}
		else{
			$img->cropImage($width, $height, $x, $y);  
			$img->setImageCompression(Imagick::COMPRESSION_JPEG);
			$img->setImageCompressionQuality(70);
			$result = $img->writeImage($path);
		}
	     $img->clear();
            
    }catch (ImagickException $e) {
        echo 'У нас проблема '. $e->getMessage(). " в файле ".$e->getFile().", строка ".$e->getLine();
    }
    return $result;
}

function getDelay($src){
    $animation = new \Imagick(realpath($src));
    $del = array();
    foreach ($animation as $frame) { 
      $delay = $animation->getImageDelay(); 

      $del[] = $delay; 
    } 
    return $del[0];
}

function deleteAllFiles($dir){
    		$list = glob($dir."/*");
    		for ($i=0; $i < count($list)-11; $i++){			
    		if (is_dir($list[$i])) deleteAllFiles ($list[$i]);
    		else unlink($list[$i]);
    		}
}