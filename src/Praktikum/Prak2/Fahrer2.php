<?php

declare(strict_types=1);
require_once './Page.php';
class Fahrer2 extends Page{
    protected function __construct(){
        parent::__construct();
    }
    public function __destruct()
    {
        parent::__destruct();
    }
    protected function getViewData():array{
        $articles = array();
        $sql = "select * from ordering";
        $name = $this->_database->query($sql);
        if(!$name) throw new Exception("Fehler beim Abfrage"+ $this->_database->error);
        while($pizza = $name->fetch_assoc()){
            $sql2 = "select status from ordered_article where ordering_id= {$pizza['ordering_id']}";
            $name2 = $this->_database->query($sql2);
            $pizza['status']= $name2->fetch_assoc()['status'];
            $articles[] = $pizza;
        }
        $name->free();
        return $articles;
    }
    protected function processReceivedData(): void
    {
        if(isset($_POST)){
            foreach($_POST as $order){
                $idAndStatus= explode('|', $order);
                $sql = "update ordered_article set status = {$idAndStatus[1]} where odering_id = {$idAndStatus[0]}";
                $name = $this->_database->query($sql);
                if(!$name)  throw new Exception("Fehler beim Lesen von " + $this->_database->error);
            }

        }

    }
    protected function generateView(): void{
        $status = array(0 => "", 1 => "", 2 => "", 3 => "", 4 => "");
        $data = $this->getViewData();
        $this->generatePageHeader("Fahrer");
        echo <<< HTML
        <h2> Fahrer </h2>
        <form action = "fahrer2.php" method = "POST"accept-charset ="utf-8">

        HTML;
        foreach($data as $item){
            $status[0] = "";
            $status[1] = "";
            $status[2] = "";
            $status[3] = "";
            $status[4] = "";
            $status[$item['status']] = "selected";
            if($status[0]==""&& $status[1]==""&& $status[4]!="selected"){
                echo <<<HTML
                <h2> Bestellung {$item['ordering_id']}</h2>
                <p> Addresse {$item['address']} </p>
                <p> Zeit {$item['ordering_time']} </p>
                <p> Status <select name ="Status{$item['ordering_id']}" id ="status">
                    <option value = "{$item['ordering_id']}|2" $status[2]>Fertig</option>
                    <option value = "{$item['ordering_id']}|3" $status[3]>Unterwegs</option>
                    <option value = "{$item['ordering_id']}|4" $status[4]>Ausgeliefert</option>
                    </select>

                </p>
            HTML;
                
            }

        }
        echo <<<HTML
        <input type="submit" id="submit" value="Bestätigen">
        </form>
    HTML;
        $this->generatePageFooter();


       
    }
    public static function main():void{
        try{
            $page = new Fahrer2();
            $page->processReceivedData();
            $page->generateView();
        }
        catch(Exception $e){
            header("Content type :text/html; charset= UTF-8");
            echo $e->getMessage();
        }
    }
}
Fahrer2::main();
