<?php

session_start();
$ses1 = $_SESSION['img'];
$ses2 = $_SESSION['cropimg'];
$ses3 = $_SESSION['textimg'];

for($i = count($ses3); $i > 0; $i--){
    if(file_exists($ses3[$i])){
        unlink($ses3[$i]);
    } 
}

for($i = count($ses2); $i > 0; $i--){
    if(file_exists($ses2[$i])){
        unlink($ses2[$i]);
    } 
}

for($i = count($ses1); $i > 0; $i--){
    if(file_exists($ses1[$i])){
        unlink($ses1[$i]);
    } 
}
session_destroy();