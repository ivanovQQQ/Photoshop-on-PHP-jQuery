<?php

          ############ПОЕХАЛИ#############
    session_start();
    error_reporting(E_ALL);// включаем вывод всех ошибок:
    mb_internal_encoding('utf-8');// устанавливаем внутреннюю кодировку скрипта 

   // если файлы  и пути  пришли:
   if (is_uploaded_file($_FILES['image']['tmp_name'])){
       
       $obj = new ImageWorker();
       
       $papka = 'windows_images';
	   if (!file_exists ($papka)) mkdir($papka, 0755, true);
       
       $tmp_gray = 'forgray';
	   if (!file_exists ($tmp_gray)) mkdir($tmp_gray, 0755, true);
      
             $width = intval($_POST['width']);
             $height = intval($_POST['height']);
             $rlc = intval($_POST['rlc']);
             $size_bord = abs(intval($_POST['size_bord']));
             $color_bord=trim($_POST['color_bord']);
             $contrast = $_POST['contrast'];
             $brightness = $_POST['brightness'];
             $contrast = intval($_POST['contrast']);
             $brightness =  intval($_POST['brightness']);
             //$gray =  intval($_POST['gray']);
             $filter = trim($_POST['filter']);
             $filterVal = intval($_POST['filterVal']);
             $speed =  intval($_POST['speed']);
             
             //echo $filter;

             $fall = $obj->get_mimeType($_FILES['image']['tmp_name'], '/image\/jpeg|image\/png|image\/gif/', '1')
             .$obj->validate($color_bord, '/\#[a-z\d]{6}/i', '1'); 
             
             if(strlen($rlc)>1 ||$width > 1280 || $height > 960 ||  strlen($color_bord)>17 ||$size_bord>100 || abs($contrast) > 100 || abs($brightness) >100 || $fall){
                 exit(json_encode(array('save' => null)));            
             }
            
    	  	// получаем имя файла без пути:
	        $name=basename($_FILES['image']['name']);
	        
            // получаем расширение файла:
	        $ext = strtolower(mb_substr ($name, mb_strrpos($name, '.')+1));
	        $filetypes = array('jpg','png','gif','jpeg');
	        // если расширения совпадают
	        if (in_array($ext, $filetypes)){
                
                $sourse = $_FILES['image']['tmp_name'];
                $saveto = $papka.'/'.strval(microtime(true)).'.'.$ext;
                $ImagickSave = __DIR__ .'/'. $saveto;
                //move_uploaded_file($sourse, $saveto);
                $geometry = $obj->getGeometry($sourse);
                //echo json_encode(array('sve' => $geometry[0], 'dlay' => $geometry[1]));
                 
                  if($rlc){
                      $obj->resize_crop($sourse, $ImagickSave, $width, $height);
                  }
                  else{
                      $obj->resize($sourse, $ImagickSave, $width, $height);
                  }
                     if($size_bord){
                         $obj->set_border($ImagickSave, $ImagickSave, $size_bord, $color_bord);
                     }
                     if($brightness || $contrast){
                         $obj->brightnessContrastImage($ImagickSave, $ImagickSave, $brightness, $contrast);
                     }
                     if($filter){
                         $obj->filter($ImagickSave, $ImagickSave, $filter, $filterVal);
                     }
                     if($speed){
                         $obj->delayImage($ImagickSave, $ImagickSave, $speed);
                     }
                     
                   //session_destroy();
                   $_SESSION['img'][] = $saveto;
                   $ses = $_SESSION['img'];
                    if(count($ses) > 1){
                       unlink($ses[count($ses)-2]);
                       //print_r($_SESSION['img']);
                    }
           if($ext === 'gif'){
               echo json_encode(array('save' => $saveto, 'delay' => $obj->getDelay($saveto)));
           }        
           else echo json_encode(array('save' => $saveto));
  
      }
} else exit(json_encode(array('save' => null)));



class ImageWorker {

function getGeometry($src){
    $animation = new \Imagick(realpath($src));
    $w = $animation->getImageWidth();
    $h = $animation->getImageHeight();
    return array($w, $h);
}



// фильтр Imagick+GD
function filter($src, $dest, $filter, $arg){
  $result = false;
  $filter = constant($filter);
  try{
    $animation = new \Imagick(realpath($src));
    $tmp = 'forgray/'.uniqid().microtime(true).'.jpg';
    if($animation->getImageMimeType()=='image/gif'){
    
    foreach ($animation as $frame) { 
    
      $frame->writeImage(__DIR__.'/'.$tmp);
      $im = imagecreatefromjpeg($tmp);
      if($filter == IMG_FILTER_PIXELATE && $arg){
          imagefilter( $im, $filter, $arg, true);
      }else{
          imagefilter( $im, $filter, $arg);   
      }
      imagejpeg($im, $tmp);
      $fr = new \Imagick(realpath($tmp));
      $frame->compositeImage($fr, Imagick::COMPOSITE_DEFAULT, 0, 0);
      unlink($tmp);
      
    } 
      $result = $animation->writeImages($dest, true);
    }else{
      $animation->writeImage(__DIR__.'/'.$tmp);
      $im = imagecreatefromjpeg($tmp);
      if($filter == IMG_FILTER_PIXELATE && $arg){
          imagefilter( $im, $filter, $arg, true);
      }else{
          imagefilter( $im, $filter, $arg);   
      }
      imagejpeg($im, $tmp);
      $fr = new \Imagick(realpath($tmp));
      $animation->compositeImage($fr, Imagick::COMPOSITE_DEFAULT, 0, 0);
      $result = $animation->writeImage($dest);
      unlink($tmp);
    }
  }catch(ImagickException $e){
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

public function delayImage($src, $dest, $speed){
    $result=FALSE;
    try{
        $imagick = new Imagick(realpath($src));
        $imagick = $imagick->coalesceImages();

        foreach ($imagick as $frame) {
            $imagick->setImageDelay($speed);
        }

        $imagick = $imagick->deconstructImages();

        $result = $imagick->writeImages($dest, true);
    }catch(ImagickException $e){
		echo 'У нас проблема '. $e->getMessage(). " в файле ".$e->getFile().", строка ".$e->getLine();
	}
    return $result;
}
    
function get_mimeType($filename, $pattern, $message){
    $finfo = finfo_open(FILEINFO_MIME_TYPE); // возвращает mime-тип
    $mime = finfo_file($finfo, $filename);
    finfo_close($finfo);
    if(!preg_match($pattern, $mime)){
        return $message;
    } else return '';
}
    
/**
* 
* @param string $orig - путь к обрабатываемой картинке
* @param string $path - путь сохранения 
* @param int $width - ширина
* @param int $height - высота
* 
* @return результат выполнения, true или false
*/
function resize($orig, $path, $width=0, $height=0){
    $result=FALSE;
	try {
		$img = new \Imagick(realpath($orig));
		if($img->getImageMimeType()=='image/gif'){
			
		foreach ($img as $frame) {
    		$frame->thumbnailImage($width, $height);    		
		}
		// Обратите внимание, writeImages вместо writeImage
		$result=$img->writeImages($path, true);
		
	}
		else{
			$img->thumbnailImage($width, $height);
			$img->setImageCompression(Imagick::COMPRESSION_JPEG);
			$img->setImageCompressionQuality(70);
			$result = $img->writeImage($path);
		}
	     $img->clear();
	} catch(ImagickException $e){
		echo 'У нас проблема '. $e->getMessage(). " в файле ".$e->getFile().", строка ".$e->getLine();
	}
	return $result;
} 
    
    // проверка введённых данных
    public function validate($value, $pattern, $message){
        if(preg_match($pattern, $value)) return '';
        else return $message;
    }
   
   
   
###################### 
// яркость и контраст:
function brightnessContrastImage($src, $dest, $brightness, $contrast, $channel=Imagick::CHANNEL_DEFAULT) {
    $result=FALSE;
    
        try {
		$img = new \Imagick(realpath($src));
		  if($img->getImageMimeType()=='image/gif'){
    	    if(method_exists('Imagick', 'brightnessContrastImage')){
        		foreach ($img as $frame) {
            		$frame->brightnessContrastImage($brightness, $contrast, $channel);
        		}
        		// Обратите внимание, writeImages вместо writeImage
        		$result=$img->writeImages($dest, true);
        		
        	}else{
                  $tmp = 'forgray/'.uniqid().microtime(true).'.jpg';
                  foreach ($img as $frame) {
                      $frame->writeImage(__DIR__.'/'.$tmp);
                      $im = imagecreatefromjpeg($tmp);
                      imagefilter ( $im, IMG_FILTER_BRIGHTNESS, $brightness);
                      imagefilter ( $im, IMG_FILTER_CONTRAST, -$contrast);
                      imagejpeg($im, $tmp);
                      $fr = new \Imagick(realpath($tmp));
                      $frame->compositeImage($fr, Imagick::COMPOSITE_DEFAULT, 0, 0);
                      unlink($tmp);
                  }
                  $result=$img->writeImages($dest, true);
             }
          }else{
            if(method_exists('Imagick', 'brightnessContrastImage')){
    			$img->brightnessContrastImage($brightness, $contrast, $channel);
               
    			$img->setImageCompression(Imagick::COMPRESSION_JPEG);
    			$img->setImageCompressionQuality(70);
    			$result = $img->writeImage($dest);
            }else{
                 $ext = strtolower(mb_substr ($src, mb_strrpos($src, '.')+1));        
                 if($ext === 'jpg')$ext = 'jpeg';
                 $func= 'imagecreatefrom'.$ext;
                 $im = $func($src);
                  
                 imagefilter ( $im, IMG_FILTER_BRIGHTNESS, $brightness);
                 imagefilter ( $im, IMG_FILTER_CONTRAST, -$contrast);
                 
                 $func = 'image'.$ext;
                 $func($im, $dest);// сохранение выходного изображения
                 imagedestroy($im);// освобождение памяти
             }
		}
	     $img->clear();
         
	} catch(ImagickException $e){
		echo 'У нас проблема '. $e->getMessage(). " в файле ".$e->getFile().", строка ".$e->getLine();
	}
	return $result;       
}
###########################
// pear install -o http://pecl.php.net/package/imagick/imagick-3.3.0RC2.tgz


// РАМКА
function set_border($src, $dest, $size, $color){
    
$result=FALSE;

	try {
		$img = new \Imagick(realpath($src));
        $bordercolor = new ImagickPixel($color);
		if($img->getImageMimeType()=='image/gif'){
			
		foreach ($img as $frame) {
    		$frame->borderImage($color, $size, $size);
		}
		// Обратите внимание, writeImages вместо writeImage
		$result=$img->writeImages($dest, true);
		
	}
		else{
			$img->borderImage($color, $size, $size);
			$img->setImageCompression(Imagick::COMPRESSION_JPEG);
			$img->setImageCompressionQuality(70);
			$result = $img->writeImage($dest);
		}
	     $img->clear();
         
	} catch(ImagickException $e){
		echo 'У нас проблема '. $e->getMessage(). " в файле ".$e->getFile().", строка ".$e->getLine();
	}
	return $result;
    
}

// большой функ по резу
function  resize_crop($source, $output, $width, $height){
$result=FALSE;
	try {
		$img = new \Imagick(realpath($source));
		if($img->getImageMimeType()=='image/gif'){
			
		foreach ($img as $frame) {
    		$frame->cropThumbnailImage($width, $height);    		
		}
		// Обратите внимание, writeImages вместо writeImage
		$result=$img->writeImages($output, true);
		
	}
		else{
			$img->cropThumbnailImage($width, $height);
			$img->setImageCompression(Imagick::COMPRESSION_JPEG);
			$img->setImageCompressionQuality(70);
			$result = $img->writeImage($output);
		}
	     $img->clear();
         
	} catch(ImagickException $e){
		echo 'У нас проблема '. $e->getMessage(). " в файле ".$e->getFile().", строка ".$e->getLine();
	}
	return $result;
}


// удаление файлов
function deleteAllFiles($dir){
    $list = glob($dir."/*");
    for ($i=0; $i < count($list); $i++){			
    	if (is_dir($list[$i])) deleteAllFiles ($list[$i]);
    	else unlink($list[$i]);
    }
}

public function execute($command)
    {
        $command = str_replace(array("\n", "'"), array('', '"'), $command);
        $command = escapeshellcmd($command);

        exec($command);
    }
    
    public function gotham($frame)
{
    $this->execute("convert $frame -modulate 120,10,100 -fill '#222b6d' -colorize 20 -gamma 0.5 -contrast -contrast $$frame");
}
    
}

/*// чёрно-белое Imagick+GD
function blackImage($src, $dest){
  $result = false;
  try{
    $animation = new \Imagick(realpath($src));
    $tmp = 'forgray/'.uniqid().microtime(true).'.jpg';
    if($animation->getImageMimeType()=='image/gif'){
    
    foreach ($animation as $frame) { 
    
      $frame->writeImage(__DIR__.'/'.$tmp);
      $im = imagecreatefromjpeg($tmp);
      imagefilter( $im, IMG_FILTER_GRAYSCALE);
      imagejpeg($im, $tmp);
      $fr = new \Imagick(realpath($tmp));
      $frame->compositeImage($fr, Imagick::COMPOSITE_DEFAULT, 0, 0);
      unlink($tmp);
      
    } 
      $result = $animation->writeImages($dest, true);
    }else{
      $animation->writeImage(__DIR__.'/'.$tmp);
      $im = imagecreatefromjpeg($tmp);
      imagefilter( $im, IMG_FILTER_GRAYSCALE);
      imagejpeg($im, $tmp);
      $fr = new \Imagick(realpath($tmp));
      $animation->compositeImage($fr, Imagick::COMPOSITE_DEFAULT, 0, 0);
      $result = $animation->writeImage($dest);
      unlink($tmp);
    }
  }catch(ImagickException $e){
		echo 'У нас проблема '. $e->getMessage(). " в файле ".$e->getFile().", строка ".$e->getLine();
	}
    return $result; 
} */     