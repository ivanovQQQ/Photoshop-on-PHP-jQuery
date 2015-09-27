<?php
header ("Content-Type:text/html; charset=UTF-8", false);
session_start();
// случайная картинка для заглушки
$images_dir='fon';
$scan=scandir($images_dir);
unset($scan[0]);
unset($scan[1]);
shuffle($scan);
$scanImg = $scan[0];

?>

<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="css/style.css"/>
<link rel="stylesheet" href="css/themes.css"/>
<style>
@font-face {font-family:"Shot1";src: url("fonts/Shot1.ttf");}
@font-face {font-family:"Shot2";src: url("fonts/Shot2.ttf");}
@font-face {font-family:"Shot3";src: url("fonts/Shot3.ttf");}
@font-face {font-family:"Shot4";src: url("fonts/Shot4.ttf");}
@font-face {font-family:"Shot5";src: url("fonts/Shot5.ttf");}
@font-face {font-family:"Shot6";src: url("fonts/Shot6.ttf");}
@font-face {font-family:"Shot7";src: url("fonts/Shot7.ttf");}
@font-face {font-family:"Shot8";src: url("fonts/Shot8.ttf");}
@font-face {font-family:"Shot9";src: url("fonts/Shot9.ttf");}
@font-face {font-family:"Shot10";src: url("fonts/Shot10.ttf");}


@font-face {font-family:"Shot11";src: url("fonts/Shot11.ttf");}
@font-face {font-family:"Shot12";src: url("fonts/Shot12.ttf");}
@font-face {font-family:"Shot13";src: url("fonts/Shot13.ttf");}
@font-face {font-family:"Shot14";src: url("fonts/Shot14.ttf");}
@font-face {font-family:"Shot15";src: url("fonts/Shot15.ttf");}
@font-face {font-family:"Shot16";src: url("fonts/Shot16.ttf");}
@font-face {font-family:"Shot17";src: url("fonts/Shot17.ttf");}
@font-face {font-family:"Shot18";src: url("fonts/Shot18.ttf");}
@font-face {font-family:"Shot19";src: url("fonts/Shot19.ttf");}
@font-face {font-family:"Shot20";src: url("fonts/Shot20.ttf");}

body{
    color:#fffbfb;  font-weight: bold;
    min-width: 1024px;
}
input{
	font-weight: bold;
	font-size: 22px;
	margin-left: 33px;
}
#textEdit table img{
    cursor: pointer;
    height: 37px;
    width: 63px;
}
#textEdit table tr td:first-child{
    width: 155px;
    text-align: left;
}
#textEdit table tr td.colors img{
    width: 37px;
    height: 37px;
}
</style>

</head>
<body style="padding: 0; margin: 0; background: #3a3c39;">

<div class="main" style="padding: 33px;">

<div style="width: 1000px; margin: auto;">
<h1 style="font-family: C3216tbU;text-align: center;">MAGICK PHOTOSHOP <a href="https://github.com/sash003/Photoshop-on-PHP-jQuery"><img src="img/1432304683.jpg"> </a></h1>
<span style="color: #769c75; font-weight: bold; font-size: 21px;">
/*<br />
* @Copyright = sash<br />
 * Если передаёте ширину и высоту, картинка будет растянута<br>
 * Передавая только один параметр, другой подставится согласно масштабу <br>
 * Ecть возможность резки по центру<br>
 * Допускаются всевозможные комбинации <br>
* Также можно выделить и сохранить нужный участок картинки<br>
* Максимальные размеры - 1280х960, для гифок от 100 до 500 в зависимости от размера (большая нагрузка на сервр)<br>  
* В случае с гифками придётся малость подождать<br>
* Здесь на сервере похоже стоит старая версия ImageMagick, метод brightnessContrastImage не доступен, я уже прям в функ ImageMagick встроил GD, долго будет обрабатывать каждый кадр, точно так же реализованы фильтры<br>
* P.S. В случае с Mozilla ВОЗВРАЩАЕТСЯ ГИФКА, почему она не всегда проигрывается - загадка природы<br>
* P.P.S. Для выполнения консольных exec команд установите <a href="http://freesoft.ru/imagemagick" style="color: white;">ImageMagick</a><br>
*/<br>
</span>
<form id="my_form" method='post' action='worker.php' enctype='multipart/form-data' style="width: 760px;">

<table>    
<tr>
    <td>jpg, png или gif файл. Суровая точка :)</td>
    <td><input type='file' name='image'  multiple='true' /></td>
</tr> 
<tr>
    <td></td>
    <td><input  type="hidden"  name="papka"   value="windows_images"/></td>
</tr>
<tr>
    <td>Ширина</td>
    <td><input type="text" name="width" value="555"/></td>
</tr>
<tr>
    <td> Высота</td>
    <td><input type="text" name="height" value=""/></td>
</tr>
<tr>
    <td>Резка по центру, любая цифра</td>
    <td><input type="text" name="rlc" value=""/></td>
</tr>    
<tr>
    <td>Сделать рамку, размеры рамки, max 100</td>
    <td><input type="text" name="size_bord" value=""/></td>
</tr>
<tr>
    <td>Цвет рамки, по умолчанию чёрный</td>
    <td><input type="text" name="color_bord" value="#000000"/></td>
</tr>
<tr>
    <td>Изменить яркость, от -100 до 100</td>
    <td><input type="text" name="brightness" value=""/></td>
</tr>
<tr>
    <td>Изменить контраст, от -100 до 100</td>
    <td><input type="text" name="contrast" value=""/></td>
</tr>
<tr>
    <td>Изменить скорость гифки, 1/100c</td>
    <td><input type="text" name="speed" value=""/></td>
</tr>
<tr>
    <td>Поднять количество кадров в гифке</td>
    <td><input type="text" name="countFrames" value=""/><span style="text-decoration: line-through; color: green; padding-left: 11px;">EXEC</span></td>
</tr>
<tr>
    <td>Установить количество кадров в гифке</td>
    <td><input type="text" name="setCountFrames" value=""/> </td>
</tr>
<tr>
    <td>Замедлить гифку (float)</td>
    <td><input type="text" name="setSlowdown" value=""/></td>
</tr>
<tr>
    <td>Убрать фон (%)</td>
    <td><input type="text" name="trimBackground" value=""/> <span style="text-decoration: line-through; color: green; padding-left: 11px;">EXEC</span></td>
</tr>
<tr>
    <td><select name="filter">
        <option></option>
        <option>IMG_FILTER_GRAYSCALE</option>
        <option>IMG_FILTER_SELECTIVE_BLUR</option>
        <option>IMG_FILTER_GAUSSIAN_BLUR</option>
        <option>IMG_FILTER_SMOOTH</option>
        <option>IMG_FILTER_PIXELATE</option>
        <option>IMG_FILTER_MEAN_REMOVAL</option>
        <option>IMG_FILTER_EMBOSS</option>
    </select></td>
    <td><input type="text" name="filterVal" value=""/></td>
</tr>
<!---->

<tr style="outline: 2px solid green;">
    <td style="text-align: center;">Наложить водяной знак</td>
    <td class="watermark">
    <input type="file" name="watermark"/><br>
    <input type="text" name="coeff" value="15"/> Процент от ширины основной<br>
    <input type="text" name="x"/> x координата<br>
    <input type="text" name="y"/> y координата<br>
    <input type="text" name="opacity" value=""/> Прозрачность (float)<br>
    </td>
</tr>

<!---->
<tr>
    <td><br><input type='submit' id='buttupload' value='Вперёд!' style="background: #359216; border: 5px solid blue; border-radius: 11px; margin-left: 55px;" /></td>
    <td></td>
</tr>
 </table>
</form>
</div>

<br style="clear:both;"/>
<br>
<div id="imgEdit" style="text-align: center;">

<span id="getimage" style="position: relative; top: -5px;"></span>

				<div id="write" style="position: relative; margin: auto;">
                <span  contenteditable="true" id="text" style="position: absolute; border: 1px solid white; text-align:left; display: none; font-family: Shot11; font-size: 22px; background: transparent;"></span>
                
                
                
                <img src="fon/<?php echo $scanImg ?>" id="thumbnail" />
                </div>
				<br style="clear:both;"/>
				<form name="thumbnail">
					<input type="hidden" name="source" value="fon/<?php echo $scanImg ?>" id="source" />
					<input type="hidden" name="x1" value="" id="x1" />
					<input type="hidden" name="y1" value="" id="y1" />
					<input type="hidden" name="x2" value="" id="x2" />
					<input type="hidden" name="y2" value="" id="y2" />
					<input type="hidden" name="w" value="" id="w" />
					<input type="hidden" name="h" value="" id="h" />
				</form>
                
                
<div id="texrEditWrap" style="text-align: left; width: 1280px; margin: auto;">
<div style="font-size: 22px; font-weight: bold;padding:4px 0 11px 0; color: #e8c180;">Пишем буковы</div>
<form id="textEdit" style="margin: auto; width: 870px; float: left;">

    <table>
        <tr>
            <td>Шрифт</td>
            <td style="width: 700px;">
            
            <img src="img/fonts/Shot1.jpg" data-font="Shot11">
            <img src="img/fonts/Shot2.jpg" data-font="Shot12">
            <img src="img/fonts/Shot3.jpg" data-font="Shot13">
            <img src="img/fonts/Shot4.jpg" data-font="Shot14">
            <img src="img/fonts/Shot5.jpg" data-font="Shot15">
            <img src="img/fonts/Shot6.jpg" data-font="Shot16">
            <img src="img/fonts/Shot7.jpg" data-font="Shot17">
            <img src="img/fonts/Shot8.jpg" data-font="Shot18">
            <img src="img/fonts/Shot9.jpg" data-font="Shot19">
            <img src="img/fonts/Shot10.jpg" data-font="Shot20">
            
            <img src="img/fonts/Shot1.jpg" data-font="Shot1">
            <img src="img/fonts/Shot2.jpg" data-font="Shot2">
            <img src="img/fonts/Shot3.jpg" data-font="Shot3">
            <img src="img/fonts/Shot4.jpg" data-font="Shot4">
            <img src="img/fonts/Shot5.jpg" data-font="Shot5">
            <img src="img/fonts/Shot6.jpg" data-font="Shot6">
            <img src="img/fonts/Shot7.jpg" data-font="Shot7">
            <img src="img/fonts/Shot8.jpg" data-font="Shot8">
            <img src="img/fonts/Shot9.jpg" data-font="Shot9">
            <img src="img/fonts/Shot10.jpg" data-font="Shot10">
            
            </td>
        </tr>
        <input type="hidden" name="font" value="Shot11"/>
        <input type="hidden" id="picked" name="color" value="#ffffff"/>
         <tr>
            <td>Величина шрифта</td>
            <td style="width: 700px;">
            <input type="text" id="sizeInp" style="margin-left: 0; width: 111px;"/>
            <span id="sizeSub" style="width: 111px; height: 37px; border: 2px inset #e8c180; border-radius:4px; padding: 5px; position: relative; top: -1px; cursor: pointer;">Применить</span>
            </td>
        </tr>
        <input type="hidden" name="size" value="22"/>
    </table>
    

            
    <input id="submitTextEdit" type="submit" value="Cделать надпись" style="text-align: center; border: 5px solid green; border-radius: 11px; font-weight: bold; font-size: 22px; background: white; color: #1b2150; position: relative; top: 11px;"/>
</form>

<div id="color-picker" class="cp-default" style="width: 240px;">
            <div class="picker-wrapper">
                <div id="picker" class="picker"></div>
                <div id="picker-indicator" class="picker-indicator"></div>
            </div>
            <div class="slide-wrapper">
                <div id="slide" class="slide"></div>
                <div id="slide-indicator" class="slide-indicator"></div>
            </div>
            
        </div>

</div>
				<br style="clear: both;"/>
               
                <div style="margin: auto; width: 222px; position: relative;top: -95px;">
                <span id="area" style="cursor: pointer; color: #769c75; font-weight: bold; font-size: 20px;">Включить ImageArea</span>
                <!--<span id="arearemove" style="cursor: pointer; color: #769c75; font-weight: bold; font-size: 20px;">Выключить</span>--><br>
                <span style="color: #769c75; font-weight: bold; font-size: 21px;">Bыделяем участок</span><br><br>
                <div style="position: relative;">
                <div id="hide" style="position: absolute; top: -5px; bottom: -5px; left: 0; right: 0; display: none;"></div>
				<span id="butSave" style="cursor: pointer; width: 100px; text-align: center; border: 5px solid green; border-radius: 11px; font-weight: bold; font-size: 27px; background: white; color: #1b2150;">Сохранить</span><br />
                </div>
                </div>
                <br>
               
                <div id="response" style="text-align: center;position: relative;top: -95px;"></div>
				
			</div>
            
</div>

<script src="scripts/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/imgareaselect-animated.css">
	<script type="text/javascript" src="scripts/jquery.imgareaselect.js"></script>
<script src="scripts/script.js"></script>
<script src="scripts/write.js"></script>
<script src="scripts/colorpicker.min.js"></script>
<script src="scripts/picker.js"></script>
<script src="scripts/clear.js"></script>

</body>
</html>
  
	
  
