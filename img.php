<?php
//parametry
$pict=$_GET["pict"];
$height=$_GET["height"];
$dir=$_GET["dir"];

//pokud uz existuje miniatura ve slozce mini, presmeruje rovnou, jinak se miniatura vytvori
if (file_exists("$dir/mini/".$pict)) header ("Location: $dir/mini/".$pict);

else {
$im=imagecreatefromjpeg("$dir/".$pict);
$x=imagesx($im);
$y=imagesy($im);

$pomer=$x/$y;

$width=$height*$pomer;

$im2=imagecreatetruecolor($height*4/3, $height);
imagecopyresampled ( $im2, $im, ($height*4/3-$width)/2, 0, 0, 0, $width, $height, $x, $y);
imagejpeg($im2,"$dir/mini/".$pict);
imagedestroy($im);
imagedestroy($im2);
echo header ("Location: $dir/mini/".$pict);
}
?>
