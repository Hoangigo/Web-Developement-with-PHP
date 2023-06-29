<?php

declare(strict_types=1);
header("Content-type: text/html")
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pizza Service</title>
</head>

<body>
    <?php
    $array = [
        "Salami" => 4.5,
        "Hawaii" => 5.5,
        "Margherita" => 5.0,
    ]
    ?>
    <h1>Bestellung</h1>
    <h2>Speisekarte</h2>
    <?php

    foreach ($array as $pizza => $price) {
        echo <<< EOT
        <article>
        <img src="photos/hawaii.jpg" alt="Bild von Pizza" width="150" height="150">
        <p>$pizza</p>
        <p>$price €</p>
        </article>
        EOT;
    }
    ?>
    <section>
        <h1>Warenkorb</h1>
        <form action="https:??echo.fbi.h-da.de" method="POST">
            <select tabindex="0" name="food" size="3" multiple>
                <option value="Salami" selected>Salami</option>
                <option value="Hawaii">Hawaii</option>
                <option value="Salami">Salami</option>
            </select>
            <p>14,5$</p>
            <input type="text" placeholder="Ihre Addresse" />
            <br></br>
            <button type="button">Alle Loeschen</button>
            <button type="button">Auswahl Loeschen</button>
            <button type="button">Bestellen</button>
        </form>
    </section>
</body>

</html>