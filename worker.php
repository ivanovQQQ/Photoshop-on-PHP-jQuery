<?php
// cd C:/OpenServer/domains/MyWorks/Photoshop-on-PHP-jQuery
// pear install -o http://pecl.php.net/package/imagick/imagick-3.3.0RC2.tgz

          ############ПОЕХАЛИ#############
          
    function myErrorHandler ($errno, $errstr, $errfile, $errline) {
        echo json_encode(array("error"=>'Ушибка: '.$errstr."Смотрим на строку ".$errline." файла ".$errfile. ", PHP " . PHP_VERSION));
        exit;
    }
    
    set_error_handler('myErrorHandler');
    
    //trigger_error("Некорректный входной вектор, пропущен массив значений", E_USER_WARNING);
    
    session_start();
    error_reporting(E_ALL);// включаем вывод всех ошибок:
    mb_internal_encoding('utf-8');// устанавливаем внутреннюю кодировку скрипта 

    

   // если файлы  и пути  пришли:
   if (is_uploaded_file($_FILES['image']['tmp_name'])){
       
       $img = new ImageWorker();
       
       $img->mkDirs();       
      
             $width = intval($_POST['width']);
             $height = intval($_POST['height']);
             $rlc = intval($_POST['rlc']);
             $size_bord = abs(intval($_POST['size_bord']));
             $color_bord=trim($_POST['color_bord']);
             $contrast = $_POST['contrast'];
             $brightness = $_POST['brightness'];
             $contrast = intval($_POST['contrast']);
             $brightness =  intval($_POST['brightness']);
    
             $filter = trim($_POST['filter']);
             $filterVal = intval($_POST['filterVal']);
             $speed =  intval($_POST['speed']);
             $countFrames =  intval(trim($_POST['countFrames']));
             $setCountFrames =  intval(trim($_POST['setCountFrames']));
             $setSlowdown    =  floatval(trim($_POST['setSlowdown']));
             $proc  =  intval(trim($_POST['trimBackground']));
             
             $x = intval($_POST['x']);
             $y = intval($_POST['y']);
             $coeff = floatval($_POST['coeff']);
             $opacity = floatval($_POST['opacity']);
            
             $fall = $img->get_mimeType($_FILES['image']['tmp_name'], '/image\/(jpeg|png|gif)/', '1')
             .$img->validate($color_bord, '/\#[a-z\d]{6}/i', '2')
             .$img->validate($filter, '/|^IMG_FILTER_.{6,17}$/', '3');
             
             if(    strlen($rlc)>1        ||
                    $width > 1280         || 
                    $height > 960         || 
                    strlen($color_bord)>17||
                    $size_bord>100        || 
                    abs($contrast) > 100  || 
                    abs($brightness) >100 ||
                    abs($filterVal) > 100 ||
                    abs($speed) > 100     || 
                    abs($x) > 1280        ||
                    abs($y) > 960         ||
                    abs($coeff) > 100     ||
                    abs($opacity) > 1     ||
                    $fall    ){
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
                $path2 = false;
                
                if(is_uploaded_file($_FILES['watermark']['tmp_name'])){
                   
                   $sourse2 = $_FILES['watermark']['tmp_name'];
                   $fall = $img->get_mimeType($sourse2, '/image\/(jpeg|png|gif)/', '1');
                         if($fall){
                             exit(json_encode(array('save' => null)));
                         }  
                         
                   $name2=basename($_FILES['watermark']['name']);
                   $ext2 = strtolower(mb_substr ($name2, mb_strrpos($name2, '.')+1));
                   $path2 = $img->tmp . '/' . microtime(true). '.' .$ext2;
                   move_uploaded_file($_FILES['watermark']['tmp_name'], $path2);                                
                }
                
                if($ext == 'jpeg' || $ext == 'jpg') $ext = 'png';
                $saveto = $img->papka.'/' .time(). '.' . $ext;
                $ImagickSave = __DIR__ .'/'. $saveto;
                
                  if($rlc){
                      $img->resize_crop($sourse, $ImagickSave, $width, $height);
                  }
                  else{
                      $img->resize($sourse, $ImagickSave, $width, $height);
                  }
                     if($size_bord){
                         $img->set_border($saveto, $ImagickSave, $size_bord, $color_bord);
                     }
                     if($brightness || $contrast){
                         $img->brightnessContrastImage($saveto, $ImagickSave, $brightness, $contrast);
                     }
                     if($filter){
                         $img->filter($saveto, $ImagickSave, $filter, $filterVal);
                     }
                     if($speed){
                         $img->delayImage($saveto, $ImagickSave, $speed);
                     }
                     
                     
                     //
                     
                     
                     if($countFrames){
                       $saveto = $img->setCountFrames($saveto, $countFrames);
                     }
                     
                     if($setCountFrames){
                       $saveto = $img->setCountFrames2($saveto, $setCountFrames);
                     }
                     
                     if($setSlowdown){
                        $saveto = $img->setSlowdown($saveto, $setSlowdown);
                     }
                     
                     
                     //
                     
                     
                     if($proc){
                         $saveto = $img->trimBackground($saveto, $proc);
                     }
                     if(file_exists($path2)){
                        $saveto = $img->add_watermark( $saveto, $path2, $img->papka, $coeff, $x, $y, $opacity );
                     }
                     
                     $img->rmDirs();
                     
                   $_SESSION['img'][] = $saveto;
                   $ses = $_SESSION['img'];
                    if(count($ses) > 1){
                      if(file_exists($ses[count($ses)-2])){
                       //unlink($ses[count($ses)-2]);   
                      }
                    }
                    
           if($ext === 'gif' || (isset($ext2) && $ext2 === 'gif')){
               echo json_encode(array('save' => $saveto, 'delay' => $img->getDelay($saveto)[0], 'countframes'=>$img->getDelay($saveto)[1]));
           }        
           else echo json_encode(array('save' => $saveto));
           
           $img->rmDirs();
           
      }
} else exit(json_encode(array('save' => null)));



class ImageWorker {
    
function mkDirs(){
    $dirs = array('windows_images', 'forgray', 'tmp', 'tmp2', 'tmp3', 'tmp4');
    foreach($dirs as $dir){
        if (!file_exists ($dir)) mkdir($dir, 0755, true);
    }
}
    
function rmDirs(){
    $dirs = array('forgray', 'tmp', 'tmp2', 'tmp3', 'tmp4');
    foreach($dirs as $dir){
        if (file_exists ($dir)){
            $this->deleteAllFiles($dir);
            rmdir($dir);
        }
    }
}
    
public $papka = 'windows_images';
public $forgray = 'forgray';
public $tmp  = 'tmp';
public $tmp2  = 'tmp2';
public $tmp3  = 'tmp3';
public $tmp4  = 'tmp4';

function setSlowdown($src, $count){
    
    $gif = new \Imagick(realpath($src));
    $delay = $this->getDelay($src)[0];
    $countFr = $this->getDelay($src)[1];
    
    if($gif->getImageMimeType()=='image/gif'){
     
     foreach($gif as $key=>$frame){
         $frame->setImageDelay($delay+$key*$count);
     }
     
     $src = $this->papka . '/' . $countFr . 'x' . $delay . 'X' . time() . '.gif';
    
    $gif->writeImages(__DIR__ . '/' . $src, true);
    
    $gif->clear();
    
    return $src;
     
    }
    
}

function setCountFrames2($src, $count){
   
    $gif = new \Imagick(realpath($src));
    if($gif->getImageMimeType()=='image/gif'){
    
    $new = new \Imagick();
    $delay = $this->getDelay($src)[0];
    $countFr = $this->getDelay($src)[1];
            
    foreach($gif as $key=>$frame){
        if($key<$count){
            $frame->writeImage(__DIR__ . '/' . $this->tmp4 . '/' . $key . '.png' );
        }
    }
            $scan = glob($this->tmp4 . '/*');
            sort($scan, SORT_NATURAL);
            foreach($scan as $file){
                
                $img = new \Imagick(realpath($file));
                $img->setImageDelay($delay);
                $new->addImage($img);
                
            }                     
    }
    
    $src = $this->papka . '/' . $countFr . 'x' . $delay . 'X' . time() . '.gif';
    
    $new->writeImages(__DIR__ . '/' . $src, true);
    
    $gif->clear();
    $new->clear();
    
    return $src;

}
    

function setCountFrames($src, $count){
   
    $gif = new \Imagick(realpath($src));
    if($gif->getImageMimeType()=='image/gif'){
    
    $delay = $this->getDelay($src)[0];
    $countFr = $this->getDelay($src)[1];
    
    for($i = 0; $i < $count; $i++){
        copy($src, $this->tmp3 . '/' . $i.'.gif');
    }
    $name = $countFr.'x'. $delay .'I' .time().'.gif';
    $name2 = '../' . $this->papka . '/' .$name;
    $name3 = $this->papka . '/' .$name;
    
    $this->execute("convert -dispose background -delay {$delay} tmp3/*.gif -loop 0 {$src}");
    
    $gif->clear();
    return $src;
    
    }
    
}
    
public function execute($command){
    
        $command = str_replace(array("\n", "'"), array('', '"'), $command);
        $command = escapeshellcmd($command);
        
        exec($command);
    }
    
function trimBackground($src, $proc){
    
$type=pathinfo($src, PATHINFO_EXTENSION);
$im = new \Imagick(realpath($src));
$color = $im->getImagePixelColor (0, 0);
$color = $color->getColorAsString(); 
$dest = $this->papka . '/' . microtime(true) . '.' . $type;
$this->execute("convert {$src} -fuzz {$proc}% -transparent {$color} {$dest}");
return $dest;

}
   
    
function add_watermark( $source_image_path, $watermark_path, $papka, $coeff, $x, $y, $opacity=0 ){
$result = false;
try{
    
	$first = new \Imagick(realpath($source_image_path)); 
	$second = new \Imagick(realpath($watermark_path));
    
    $out = $papka . '/' . microtime(true). '.gif';
    $output = __DIR__ .'/'. $out;
    
    $width = $first->getImageWidth();
    $width = $width/100*$coeff;
    
    if(!$x && !$y){
        $width_src = $first->getImageWidth(); 
        $width_mark = $second->getImageWidth();
    	$height_src = $first->getImageHeight();
    	$height_mark = $second->getImageHeight();
        $coef = $width_mark/$height_mark;
        
    	$x = $width_src - $width-5;
    	$y = $height_src - $width/$coef-5;
    }
    
	if($first->getImageMimeType()=='image/gif'){
        
        if($second->getImageMimeType()=='image/gif'){
            
            ##########
            //////////
            foreach($second as $frame){
                $frame->thumbnailImage($width, 0);
            }
            
            $tmppath = __DIR__ . '/' . $this->tmp . '/' . uniqid() . microtime(true).'.gif';
            $second->writeImages($tmppath, true);
            
            $second = new \Imagick($tmppath);
            foreach($second as $key=>$frame){
                $frame->writeImage(__DIR__. '/' . $this->tmp2 . '/' .$key . '.png');
            }
            
            $scan = glob($this->tmp2 . '/*');
            sort($scan, SORT_NATURAL);
            
            foreach($first as $key=>$frame){
                if(array_key_exists($key, $scan)){
                    $new = new \Imagick(realpath($scan[$key]));
                    if($opacity) $new->setImageOpacity( $opacity );
                }else $new = new \Imagick(realpath($scan[0]));
                
                //накладываем изображения
                $frame->compositeImage($new, imagick::COMPOSITE_DEFAULT, $x, $y); 	
                
            }
            $result = $first->writeImages($output, true);
            //////////
            ##########
            
        }else{
        $second->thumbnailImage($width, 0);
	    if($opacity) $second->setImageOpacity( $opacity );

		// то для каждого фрейма гифки делаем следующее:
		foreach ($first as $frame) {
  
            //накладываем изображения
        	$frame->compositeImage($second, imagick::COMPOSITE_DEFAULT, $x, $y);
    	}
		$result=$first->writeImages($output, true);
      }
	}else{
        
        if($second->getImageMimeType()=='image/gif'){
            
            $new = new \Imagick();
            
            $delay = $this->getDelay($watermark_path)[0];
            
            foreach($second as $frame){
                $frame->thumbnailImage($width, 0);
            }
            $tmppath = __DIR__ . '/' . $this->tmp . '/' . uniqid() . microtime(true).'.gif';
            $second->writeImages($tmppath, true);
            
            $second = new \Imagick($tmppath);
            
            foreach($second as $frame){

	            if($opacity) $frame->setImageOpacity( $opacity );
                $firt = clone $first;
                
                //накладываем изображения
                $firt->compositeImage($frame, imagick::COMPOSITE_DEFAULT, $x, $y); 	
                $firt->setImageDelay($delay);
                $new->addImage($firt);
            }
            $result = $new->writeImages($output, true);
            unlink($tmppath);
        }else{
    		
            $second->thumbnailImage($width, 0);
            if($opacity) $second->setImageOpacity( $opacity );
    	
    		$first->compositeImage($second, imagick::COMPOSITE_DEFAULT, $x, $y); 	
    		//устанавливаем степень сжатия
    		$first->setImageCompression(Imagick::COMPRESSION_JPEG);
    		//и качество 
    		$first->setImageCompressionQuality(90);
    		$result = $first->writeImage($output);		
    	}
	
	}

$first->clear();
$second->clear();

}
catch(Exception $e){
		echo 'У нас проблема '. $e->getMessage(). " в файле ".$e->getFile().", строка ".$e->getLine();
	}
return $out;
}


// фильтр Imagick+GD
function filter($src, $dest, $filter, $arg){
  $result = false;
  $filter = constant($filter);
  try{
    $animation = new \Imagick(realpath($src));
    $tmp = $this->forgray . '/'.uniqid().microtime(true).'.jpg';
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
  
    }
  }catch(ImagickException $e){
		echo 'У нас проблема '. $e->getMessage(). " в файле ".$e->getFile().", строка ".$e->getLine();
	}
    return $result; 
}
   
function getDelay($src){
    $animation = new \Imagick(realpath($src));
    $del = array();
    $countFrames = 0;
    foreach ($animation as $frame) { 
      $delay = $animation->getImageDelay(); 
      $countFrames++;
      $del[] = $delay; 
    } 
    return array($del[0], $countFrames, $del[0]*$countFrames);
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
                  $tmp = $this->forgray . '/'.uniqid().microtime(true).'.jpg';
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

function getGeometry($src){
    $animation = new \Imagick(realpath($src));
    $w = $animation->getImageWidth();
    $h = $animation->getImageHeight();
    return array($w, $h);
}

// удаление файлов
function deleteAllFiles($dir){
    $list = glob($dir."/*");
    for ($i=0; $i < count($list); $i++){			
    	if(file_exists($list[$i])){
          if (is_dir($list[$i])) deleteAllFiles ($list[$i]);
    	  else unlink($list[$i]);   
        }
    }
}
    
}// end class Worker


	
