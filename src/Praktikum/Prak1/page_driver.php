<?php
$language = "de";
$orders = array(
    array(
        "id" => 1,
        "status" => "ordered",
        "type" => "Margherita",
        "adress" => "Hans",
    ),
    array(
        "id" => 2,
        "status" => "ordered",
        "type" => "Salami",
        "adress" => "Hans",
    ),
    array(
        "id" => 3,
        "status" => "done",
        "type" => "Margherita",
        "adress" => "Hans",
    )
);
echo <<<EOT
<!DOCTYPE html>
<html lang= "$language">
<head>
    <meta charset="UTF-8">
    <titel> Fahrerseite </titlel>
</head>
<body>
    <section id= "main">
    <article>
    <h1>Fahrer   </h1>
    <form action="https://echo.fbi.h-da.de/" method="POST">
        
EOT;
foreach ($orders as $row) {
    echo <<< EOT
    <section>
    <h2>Bestellnummer $row[id]: Pizza $row[type] </h2>
    <p> Name/ Adress: $row[adress] <?p>
    <p> Status: <select name="status">
    <option value= "ordered">Bestellt</option>
    <option value= "oven">Im Opfen</option>
    <option value= "done">Fertig</option>
    <option value= "moving">Unterwegs</option>
    <option value= "delivered">Ausgeliefert</option>
    </select>
    </p>
    </section>
    </form>
    </article>

    EOT;
}
echo <<< EOT
    <input type="submit" id= "submit" value= "Bestätigen">
    </section>
    </body>
    </html>
EOT;
