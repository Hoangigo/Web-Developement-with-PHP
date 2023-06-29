<?php

declare(strict_types=1);
require_once './Page.php';
class Order2 extends Page{
    protected function __construct(){
        parent::__construct();
    }
    public function __destruct()
    {
        parent::__destruct();
    }
    protected function getViewData():array{
        $articles = array();
        $sql = "select * from article";
        $name = $this->_database->query($sql);
        if(!$name) throw new Exception("Fehler beim Abfrage"+ $this->_database->error);
        while($pizza = $name->fetch_assoc()){
            $articles[] = $pizza;
        }
        $name->free();
        return $articles;
    }
    protected function processReceivedData(): void
    {
        parent::processReceivedData();
        //TODO check address and warenkorb
        if(isset($_POST["address"])&& isset($_POST["warenkorb"])){
            $sql = "INSERT INTO ordering(address) VALUES ('" . $_POST["address"] . "')";
            $name = $this->_database->query($sql);
            if(!$name) throw new Exception("Fehler beim Insert"+ $this->_database->error);
            $sql1 = "SELECT ordering_id from ordering where address = '" . $_POST["address"] . "'";
            $order = $this->database->query($sql1);
            if(!$order) throw new Exception("Fehler beim Abfrage"+ $this->_database->error);
            $orderid = $order->fetch_assoc();
            if(!$orderid) throw new Exception("Fehler beim Abfrage"+ $this->_database->error);
            foreach($_POST["warenkorb"] as $temp){
                $sql3 =  "INSERT INTO orderedarticle(orderingid,article_id, status) VALUES ($orderid[ordering_id],$temp,0)";
                $name = $this->database->query($sql3);
                if(!$name) throw new Exception("Fehler beim Insert"+ $this->_database->error);

            }
            $order->free();

        }

    }
    protected function generateView(): void{
        $endPreis= 0;
        $data = $this->getViewData();
        $this->generatePageHeader("Bestellung");
        echo <<< HTML
            <h1>Bestellung</h1>
        HTML;
        foreach ($data as $pizza){
            echo <<<HTML
            <img src ="photos/{$pizza['picture']}" alt ="" width="100" height ="100">
            <p> Art : {$pizza['name']}</p>
            <p> Preis: {$pizza['price']}</p>
            HTML;
        }
        echo <<< HTML
        <h2> Warenkorb </h2>
        <form action = "order2.php" method = "POST"accept-charset ="utf-8">
            <section>
                <select name= "warenkorb[]" size="6" multiple tabindex ="1">
                <option selected value="1">Salami</option>
                <option value="2">Vegetaria</option>
                <option value="3">Spinat-Huenchen</option>
                <input type="text" name="address" placeholder="Ihre Adresse" required>
            </section>
            <p>$endPreis</p>
            <input type="button" id="deleteAll" value="Alles Löschen">
            <input type="button" id="delete" value="Auswahl Löschen">
            <input type="submit" id="order" value="Bestellen">
        </form>
        HTML;

        $this->generatePageFooter();


       
    }
    public static function main():void{
        try{
            $page = new Order2();
            $page->processReceivedData();
            $page->generateView();
        }
        catch(Exception $e){
            header("Content type :text/html; charset= UTF-8");
            echo $e->getMessage();
        }
    }
}
Order2::main();
