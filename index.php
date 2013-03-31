<?php
/*
 * PHPminiGallery
 *
 * Copyright (c) 2013 Smitka development (smitka.org) && Lynt services (lynt.cz)
 *
 * $Rev: 50$
 */ 
$slozky=(!isset($_GET["dir"]))?1:0;
//Todo: možnost využít více složek
$galerie=(isset($_GET["galerie"]))?$_GET["galerie"]:"gallery";
//$galerie="gallery";
$folders=$items=$images="";
//policko text
$text="";
$ratio=4/3;

//Todo: pøedìlat vytvoøení nové galerie
if(!$slozky){
$relative_dir=$_GET["dir"];
$dir = "$galerie/".$relative_dir;
if (!file_exists($dir)) die("Galerie neexistuje");
if (!file_exists($dir."/conf.txt")){
if (file_exists($dir."/mini")){

$f=fopen($dir."/conf.txt","w+");
$conf_nadpis=(isset($_POST["nadpis"]))?$_POST["nadpis"]:"Galerie";
$conf_height=(isset($_POST["height"]))?$_POST["height"]:"120";
$conf_text=(isset($_POST["text"]))?$_POST["text"]:"";

fwrite($f,"$conf_nadpis\n");
fwrite($f,"$conf_height\n");
$eff=(isset($_POST["eff"]))?"effects":"noeff";
fwrite($f,"$eff\n");
fwrite($f,"$conf_text\n");
fwrite($f,"----------------\n");
if(isset($_POST["popis"]) && $_POST["popis"]){

	$dh  = opendir($dir."/");
	while (false !== ($filename = readdir($dh))) {
	 		if (strpos(strtolower($filename),".jpg") || strpos(strtolower($filename),".jpeg")) {
				$exif = exif_read_data("$dir/$filename");
				if(isset($exif['ImageDescription']))
					$desc = $exif['ImageDescription'];
				else $desc="";
				fwrite($f,"$filename\n");
				fwrite($f,"$desc\n");
				
			}

	}
}
fclose($f);
chmod($dir."/conf.txt", 0666);
}
else{
//Todo: pøedvytvoøit miniatury + možnost pøidat popisky
mkdir($dir."/mini");
echo "<form action=\"?dir={$_GET["dir"]}\" method=\"POST\">";
echo "<input type=\"text\" value=\"Nadpis\" name=\"nadpis\"> :: nadpis galerie<br>";
echo "<input type=\"text\" value=\"120\" name=\"height\"> :: výška miniatur<br>";
echo "<input type=\"text\" value=\"\" name=\"text\"> :: popis galerie<br>";
echo "Popisky <input type=\"radio\" value=\"0\" selected=\"selected\" name=\"popis\">nevytváøet";
if (extension_loaded("exif")) echo " <input type=\"radio\" value=\"1\" name=\"popis\">z EXIF";
//echo " <input type=\"radio\" value=\"2\" name=\"popis\"> prùvodce";
echo"<br>";
echo "Efekty <input type=\"checkbox\" value=\"1\" name=\"eff\"><br>";
echo "<input type=\"submit\" value=\"Uložit\">";
echo "</form>";
die();
}
}
$conf=file($dir."/conf.txt");
$conf=array_map('trim',$conf);
$nadpis=$conf[0];
$height=$conf[1];

//nacist text
//dalsi radek v konfigu
$text=$conf[3];
//z externiho souboru
//if(file_exists($dir."/info.txt")) $text=file_get_contents($dir."/info.txt");

$effects=(trim($conf[2])=="effects")?1:0;
$effects=(isset($_GET["effects"]))?$_GET["effects"]:$effects;
}
else {
$nadpis="Seznam galerií";
$effects=0;
}
//Todo: volba z vice sablon
//pro php  4.3+
//$sablona=file_get_contents("sablona.php");
$sablona=implode("",file("sablona.phtml"));

$foo="&nbsp;";
$echo="";
$require ="<script type=\"text/javascript\" src=\"js/jquery.js\"></script>\n";
$require.="<script type=\"text/javascript\" src=\"js/slimbox2-lynt.js\"></script>\n";
$require.=" <link rel=\"stylesheet\" type=\"text/css\" href=\"css/slimbox2.css\" media=\"screen\" />\n";
$require.=" <script type=\"text/javascript\">\n";
$require.="  var effects={$effects};\n";
$require.="  if(effects==-1) jQuery.fx.off = true;";
$require.="    $(function() {\n";
$require.="        //$('#galerie a').lightBox({txtImage: 'Obr&aacute;zek',	txtOf: 'z'});\n";
$require.="        $('#galerie a').slimbox({counterText: 'Obr&aacute;zek {x} z {y}', captionAnimationDuration: 200});\n";
$require.="    });\n";
$require.="</script>\n";
$require.="<script type=\"text/javascript\" src=\"js/gal.js\"></script>\n";

//$test=preg_match("/\[images\](.*)\[image\](.*)\[\/image\](.*)\[\/images\]/s",$sablona,$out);
//var_dump ($out);
$kde=($galerie=="gallery")?"":"galerie=$galerie&amp;"; 

//vypis seznamu galerii
if($slozky){

	$test=preg_match("/\[items\](.*)\[item\](.*)\[\/item\](.*)\[\/items\]/s",$sablona,$out);
	$dh  = opendir("$galerie/");
	while (false !== ($filename = readdir($dh))) {
		$i = "$galerie/" . $filename;
 	 if (is_dir($i))  $dirs[] = $filename;
	}
	closedir($dh);
	sort($dirs);
	for ($i=2;$i<sizeof($dirs);$i++)
		if (file_exists("$galerie/".$dirs[$i]."/conf.txt")){
		$conf=array_map('trim',file("$galerie/".$dirs[$i]."/conf.txt"));
		$height=$conf[1];
		$mini=$out[2];
		if(preg_match("/\[img_(\d+)\]/",$out[2],$param)){
			$files=array();
			$dh  = opendir("$galerie/".$dirs[$i]."/");
			while (false !== ($filename = readdir($dh))) {
  			if (strpos(strtolower($filename),".jpg") || strpos(strtolower($filename),".jpeg"))  $files[] = $filename;
			}
			closedir($dh);
			sort($files);
			$index=(!$param[1])?(rand(1,sizeof($files))-1):$param[1]-1;
			$mini=preg_replace("/\[img_(\d+)\]/","<img src=\"$galerie/".$dirs[$i]."/mini/".$files[$index]."\" height=\"$height\" width=\"".($height*$ratio)."\" />",$out[2]);
	
		}
		$echo.=str_replace(array("[dir]","[name]"),array("?".$kde."dir={$dirs[$i]}",$conf[0]),$mini);
		
	}
	$items=$out[1].$echo.$out[3];
}
//vypis obrazku
else{

	$back=dirname($relative_dir);
	if($back=='.') $back="";
	else $back="dir=$back";

	$test=preg_match("/\[images\](.*)\[image\](.*)\[\/image\](.*)\[\/images\]/s",$sablona,$out);
	$files=array();
	$dirs=array();
	//slozka, ktera se ma prohledat
	$dh  = opendir($dir."/");
	while (false !== ($filename = readdir($dh))) {
	 		if (strpos(strtolower($filename),".jpg") || strpos(strtolower($filename),".jpeg"))  $files[] = $filename;
  		if (is_dir($dir."/".$filename) && $filename!='.' && $filename!='..' && $filename!='mini') $dirs[]= $filename;
	}
	closedir($dh);
	sort($files);
	//podslozky
	if(sizeof($dirs)){
		if(preg_match("/\[folders\](.*)\[item\](.*)\[\/item\](.*)\[\/folders\]/s",$sablona,$out2)){
		$echo2="";
		foreach($dirs as $fold){
			$mini=$out2[2];
			$name="<em>$fold</em>";
			if (file_exists("$dir/$fold/conf.txt")){
				$conf=array_map('trim',file("$dir/$fold/conf.txt"));
				$name=$conf[0];	
			}
			
			if(preg_match("/\[img_(\d+)\]/",$out2[2],$param)){
				$files2=array();
				$dh2  = opendir("$dir/$fold/");
				while (false !== ($filename2 = readdir($dh2))) {
  				if (strpos(strtolower($filename2),".jpg") || strpos(strtolower($filename2),".jpeg"))  $files2[] = $filename2;
				}
			closedir($dh2);
			sort($files2);
			$index=(!$param[1])?(rand(1,sizeof($files2))-1):$param[1]-1;
			$mini=preg_replace("/\[img_(\d+)\]/","<img src=\"$dir/$fold/mini/".$files[$index]."\" height=\"$height\" width=\"".($height*$ratio)."\" />",$out2[2]);
	
			}
			$echo2.=str_replace(array("[dir]","[name]"),array("?".$kde."dir=$relative_dir/$fold",$name),$mini);

		}
		$folders=$out2[1].$echo2.$out2[3];
		}
	}
	
	foreach($files as $mini)
	{
		//vypis jpegu ze slozky, vytovreni miniatur pomoci souboru img.php a odkazy na originaly (parametr height=xxx, je vyska miniatury)

		$t=array_search($mini,$conf);
		$popis=($t)?$conf[$t+1]:"";
		
		$echo.=str_replace(array("[target]","[title]","[mini]"),array("$dir/$mini","$popis","<img height=\"$height\" width=\"".($height*$ratio)."\" src=\"img.php?dir=$dir&amp;height=$height;&amp;pict=$mini\" title=\"$popis\" alt=\"$popis\">"),$out[2]);
	
	}
	$foo="<a href=\"?$back\" title=\"na seznam galerií\">zpìt</a>";
	$images=$out[1].$echo.$out[3];
}



$sablona=preg_replace("/\[images\](.*)\[\/images\]/s",$images,$sablona);
$sablona=preg_replace("/\[folders\](.*)\[\/folders\]/s",$folders,$sablona);
$sablona=preg_replace("/\[items\](.*)\[\/items\]/s",$items,$sablona);
$sablona=str_replace(array("[require]","[nadpis]","[text]","[bottom]"),array($require,$nadpis,$text,$foo),$sablona);
echo $sablona;

?>
