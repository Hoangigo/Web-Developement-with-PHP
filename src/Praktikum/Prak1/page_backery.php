<?php
$language = "de";
//test array
$orders = array(
    array(
        "id" => 1,
        "status" => "ordered",
        "type" => "Margherita",
        "adress" => "Hans",
    ),
    array(
        "id" => 2,
        "status" => "oven",
        "type" => "Margherita",
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
<html lang="$language">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <section id="Main">
        <h1>Backstube</h1>
        <form action="https://echo.fbi.h-da.de/" method="POST">
EOT;
//html for every parameter from $orders
$status = array(0 => "", 1 => "", 2 => "", 3 => "", 4 => "");
foreach ($orders as $row) {
    $status[0] = "";
    $status[1] = "";
    $status[2] = "";
    $status[3] = "";
    $status[4] = "";
    //set status to parameter from aray
    //$status[$row['state']]="checked";

    echo <<<EOT
        <section>
        <h2>Bestellung $row[id]: Pizza $row[type]</h2>
        <p>Bestellt  <input type="radio" id="ordered" name = "$row[id]"   value="ordered"  $status[0]> </p>
        <p>Im Ofen  <input type="radio" id="oven" name = "$row[id]"   value="oven"  $status[1]> </p>
        <p>Fertig  <input type="radio" id="done" name = "$row[id]"   value="done"  $status[2]> </p>
        </section>    
EOT;
}
echo <<<EOT
        <input type="submit" id="submit" value="Bestätigen">
        </form>
    </section>
</body>
</html>
EOT;
