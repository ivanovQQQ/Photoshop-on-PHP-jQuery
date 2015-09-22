<?php

session_start();
error_reporting(E_ALL);

$src   = trim($_POST['src']);
$text  = str_replace('<div>', '\n', $_POST['text']);
$text  = str_replace('</div>', '', $text);
$text  = str_replace('&nbsp;', ' ', $text);
$text  = preg_replace('/\<br\>|\<br\/\>/', '', $text);
//echo $text;
$x     = intval($_POST['x']);
$y     = intval($_POST['y']);
$font  = trim($_POST['font']);
$size  = intval($_POST['size']);
$color = trim($_POST['color']);

$obj = new workerText;

$fall = $obj->get_mimeType($src, '/image\/(jpeg|png|gif)/', 'Файл не является изображением<br>').
        $obj->validate($color, '/#[a-z\d]{6}/i', 'неправильный цвет<br>').
        $obj->validate($font, '/Shot\d{1,2}/', 'неправильный фонт<br>');
if($fall)exit($fall);

$papka = 'textImages';
if (!file_exists ($papka)) mkdir($papka, 0755, true);

$ext  =  mb_substr ($src, mb_strrpos($src, '.')+1);
$file =  microtime(true). '.' . $ext;
$path =  'textImages/' . $file;
$fullpath = __DIR__ . '/' . $path;

$_SESSION['textimg'][] = $path;

if($obj->setText($src, $fullpath, $text, $font, $color, $size, $x, $y)){
    echo $path;
}




class workerText {
    
    public function setText($orig, $path, $text, $font, $color, $size, $x, $y){
        $result=FALSE;
        $draw = new ImagickDraw();
        $draw->setFillColor($color);

        /* Настройки шрифта */
        $draw->setFont(realpath('fonts/'.$font.'.ttf'));
        $draw->setFontSize( $size );
        $y = $y+$size;
    	try {
    		$img = new \Imagick(realpath($orig));
    		if($img->getImageMimeType()=='image/gif'){
    	    
    		foreach ($img as $frame) {
                
                $textArr = explode('\n', $text); 
                for($i = 0; $i < count($textArr); $i++){
                    $frame->annotateImage($draw, $x, $y + $size*$i, 0, $textArr[$i]); 
                }
                
                /* Создаем текст */
                  		
    		}
    		// Обратите внимание, writeImages вместо writeImage
    		$result=$img->writeImages($path, true);
    		
    	}
    		else{
                
                $textArr = explode('\n', $text); 
                for($i = 0; $i < count($textArr); $i++){
                    $img->annotateImage($draw, $x, $y + $size*$i, 0, $textArr[$i]); 
                }
                
    			//$img->annotateImage($draw, $x, $y, 0, $text);
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
    
    public function get_mimeType($filename, $pattern, $message){
    $finfo = finfo_open(FILEINFO_MIME_TYPE); // возвращает mime-тип
    $mime = finfo_file($finfo, $filename);
    finfo_close($finfo);
        if(!preg_match($pattern, $mime)){
            return $message;
        } else return '';
    }
    
    // удаление файлов
function deleteAllFiles($dir){
    $list = glob($dir."/*");
    for ($i=0; $i < count($list)-11; $i++){			
    	if (is_dir($list[$i])) deleteAllFiles ($list[$i]);
    	else unlink($list[$i]);
    }
}
    
}

