<!-- Tuulinäyttö ver 1.0.0 | Mika Autio, Valakia Interactive Oy -->
<!-- Säätieto: OpenWeatherMap API -->

<html>
<head>
   <meta charset="utf-8">
   <title>Tuuli</title>
   <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container"><div class="inner">
    <?php 
    // require_once(__DIR__ . '/img.php');
    // defined( 'ABSPATH' ) or die();

    $city = kauhava; // Muokkaa tähän haluttu kaupunki. Muista ääkköset eli Seinäjoki on seinaejoki ja kaksiosaiset nimet näin: new_york

// OpenWeatherMap API -haku määritellään tässä
    $apiurl = 'http://api.openweathermap.org/data/2.5/weather?APPID=844ec52fd7f49c86d89c303e72748e20&units=metric&q=' . $city;
    $response = file_get_contents($apiurl);
    $weather = json_decode($response);

// Haetaan tiedoista tuulen suunan astelukema ja sanitoidaan että jää vain numerot, lisäksi käännetään suunta erottamalla asteluku 360:sta
    $rotang = $weather->wind->deg;
    $sanitized_ang = filter_var($rotang, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $wind = 360 - $sanitized_ang;

// Haetaan tiedoista tuulen nopeus ja sanitoidaan että jää vain numerot sekä pyöristetään ja tiputetaan desimaalit
    $wspeed = $weather->wind->speed;
    $sanitized_wspeed = filter_var($wspeed, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $rounded_wspeed = round($sanitized_wspeed, 0);

// Määritellään nuolikuva, käännetään sitä haetun asteluvun verran ja tulostetaan se muuttujaan sekä tyhjennetään muisti
    $filename = 'arrow.png';
    $source = imagecreatefrompng($filename) or die('Error opening file ' . $filename);
    imagealphablending($source, false);
    imagesavealpha($source, true);

    $rotation = imagerotate($source, $wind, imageColorAllocateAlpha($source, 0, 0, 0, 127));
    imagealphablending($rotation, false);
    imagesavealpha($rotation, true);

    //header('Content-type: image/png');

    ob_start();
    imagepng($rotation);
    $imagedata = ob_get_contents();
    ob_end_clean();

    imagedestroy($source);
    imagedestroy($rotation);

    echo '<img src="data:image/png;base64,' . base64_encode($imagedata) . '" />';
    echo '<div class="cover"><h1>' . $rounded_wspeed . '</h1></div>';
    ?>
</div></div>
</body>
</html>