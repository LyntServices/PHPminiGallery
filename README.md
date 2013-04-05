PHPminiGallery
==============

Jednoduchý skript na jednoduché vytváření fotogalerií na webu. Snahou toho projektu je vytváření galerie co nejvíce zjednodušit.
Jako nejpohodlnější se ukázal způsob nahrát fotky přes FTP na web, nastavit nadpis a případně další drobnosti a o více se nestarat.
Přesně tento postup využívá PHPminiGallery. Pro generování galerií využívá funkce PHP na straně serveru a pro pěkné zobrazení na straně klienta knihovnu jQuery a modifikovaný lightbox script Slimbox2.

<h3>Použití</h3>
Galerie se tvoří z podsložek umístěných ve složce gallery, pro zobrazení galerie je třeba zavolat soubor index.php?dir=podslozka. Podložka obsahuje velké fotografie a po prvním přístupu se vytvoří konfigurační soubor conf.txt a složka mini s miniaturami.
Po nakopírování obrázků do podsložky se při prvním přístupu ke galerii zobrazí jednoduchý formulář k vyplnění nadpisu galerie, výšky miniatur, popisku galerie a případnému zobrazení efektů.

Po jeho vyplnění je již galerie plně funkční a zobrazí se na hlavní stránce PHPminiGallery.

<h4>Konfigurační soubor:</h4>
Každá podsložka s fotkami má svůj konfigurační soubor ve tvaru:

```
TEST
120
effects
popis galerie
thumb:url
------------
image2.jpg
Druhý obrázek v pořadí
```

Důležité jsou první 3 řádky - nadpis galerie, výška miniatury v pixelech, klíčové slovo effects (cokoliv jiného efekty vypne),
4. řádek může obsahovat popis galerie. 5. řádek může obsahovat volbu pro vlastní náhledový obrázek galerie s klíčovým slovem *thumb:* na začátku -  buď s absolutním URL (např. thumb:http://internal.lynt.cz/lynt-logo.png) nebo relativně k souboru index.php (např. thumb:gallery/photos/mini/image1.jpg).
Další řádky mohou obsahovat popisky obrázků - vždy jméno souboru a následujicí řádek jeho popisek.
Zobrazení efektů je možné explicitně vypout/zapnout parametrem effects: 
```
index.php?dir=podslozka&effects=X (X=0 vyp/X=1 zap/X= -1 úplně vyp)
```

<h3>Šablony</h3>
PHPminiGallery použivá šablonovací systém pro jednoduché přizpůsobení galerie k obrazu svému.
```
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=windows-1250">
<title>[nadpis]</title> <- prvek nadpis s názvem galerie;
[require] <- nutný prvek načítající zobrazovač galerie;
<link rel="stylesheet" type="text/css" href="css/styl2.css" media="screen" />
</head>
<body>
[images] <- sekce s obrázky samotné galerie;
<h1>[nadpis]</h1>
<div id="text">[text]</div> <- popis galerie;
[folders] <- podsekce s výpisem podsložek;
<div id="seznam">
<ul>
[item] <- sekce zobrazení podsložky;
<li><a href="[dir]">[name]</a></li> <- prvky dir (cesta ke složce s galerií), name (jméno galerie), lze využít i prvek img_X - viz seznam galerií;
[/item]
</ul>
</div>
[/folders]
<div id="galerie">
[image] <- sekce miniatury, bude vytvořena pro každou miniaturu;
<a href="[target]" title="[title]">[mini]</a> <- prvky target (cesta k originálnímu obrázku), title (jeho popisek, volitelný), mini (vytvorena miniatura);
[/image]
</div>
[/images]

[items] <- sekce seznamu galerií;
<h1>Seznam galerií</h1>
<div id="seznam">
[item] <- sekce s položkami seznamu;
<a href="[dir]">[img_0]<br />[name]</a> <- prvky dir (cesta ke složce s galerií), name (jméno galerie), img_X (0 = náhodná minatura, 1 = první obrázek ve složce, 2 = druhý atd.);
[/item]
</div>
[/items]
<div id="foo">[bottom]</div> <- prvek s tlačítkem zpět
<div id="foo2"><a href="http://lynt.cz" title="sítě, servery, webové aplikace a marketing">&copy 2013 Lynt services</a></div>
</body>
</html>
```

Původní projekt: http://smitka.org/programovani/phpminigallery
