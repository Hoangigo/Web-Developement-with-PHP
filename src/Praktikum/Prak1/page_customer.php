<?php
$language = "de";
$state = array("ordered" => "", "oven" => "", "done" => "", "moving" => "", "delivered" => "");
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
    <titel> Kundenseite </titlel>
</head>
<body>
    <section>
        
EOT;
foreach ($orders as $row) {
    $state["ordered"] = "";
    $state["oven"] = "";
    $state["done"] = "";
    $state["moving"] = "";
    $state["delivered"] = "";
    echo <<< EOT
    <section>
    <h2>Bestellung $row[id]: Pizza $row[type] </h2>
    <p>Bestellt <input type="radio" id="ordered" disabled  name= $row[id] value="ordered" $state[ordered]></p>
    <p>Im Ofen <input type="radio" id="in_progress" disabled name= $row[id] value="oven" $state[oven]></p>
    <p>Fertig <input type="radio" id="ready_to_deliver" disabled name= $row[id] value="done" $state[done]></p>
    <p>Unterwegs <input type="radio" id="on_way" disabled name= $row[id] value="moving" $state[moving]></p>
    <p>Geliefert <input type="radio" id="delivered" disabled name= $row[id] value="delivered" $state[delivered]></p>

    </section>

    EOT;
}
echo <<< EOT
    </section>
    </body>
    </html>
EOT;
