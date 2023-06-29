function process(JSONString){
    orders = JSON.parse(JSONString);
    for(let order of orders) {
        var ids = ["ordered|", "in_progress|", "ready_to_deliver|", "on_road|", "delivered|"];
        console.log(order.ordered_article_id);
        document.getElementById(ids[0].concat(order.ordered_article_id)).checked = false;
        document.getElementById(ids[1].concat(order.ordered_article_id)).checked = false;
        document.getElementById(ids[2].concat(order.ordered_article_id)).checked = false;
        document.getElementById(ids[3].concat(order.ordered_article_id)).checked = false;
        document.getElementById(ids[4].concat(order.ordered_article_id)).checked = false;

        switch(order['status']){
            case '0' :document.getElementById(ids[0].concat(order.ordered_article_id)).checked = true;break;
            case '1' :document.getElementById(ids[1].concat(order.ordered_article_id)).checked = true;break;
            case '2' :document.getElementById(ids[2].concat(order.ordered_article_id)).checked = true;break;
            case '3' :document.getElementById(ids[3].concat(order.ordered_article_id)).checked = true;break;
            case '4' :document.getElementById(ids[4].concat(order.ordered_article_id)).checked = true;break;
            default: break;
        }
    }
}

var request = new XMLHttpRequest(); 

function requestData() { // fordert die Daten asynchron an
    console.log("request");
    request.open("GET", "kundenstatus.php"); // URL fÃ¼r HTTP-GET
    request.onreadystatechange = processData; //Callback-Handler zuordnen
    request.send(null); // Request abschicken
}

function processData() {
    console.log("processData");
    if(request.readyState == 4) { // Uebertragung = DONE
       if (request.status == 200) {   // HTTP-Status = OK
            if(request.responseText != null) 
                process(request.responseText);// Daten verarbeiten
            else console.error ("Dokument ist leer");        
       } 
       else console.error ("Uebertragung fehlgeschlagen");
    } 
    else;          // Uebertragung laeuft noch
}