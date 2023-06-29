<?php

declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
/**
 * Class PageTemplate for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 *
 * PHP Version 7.4
 *
 * @file     PageTemplate.php
 * @package  Page Templates
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 * @version  3.0
 */

// to do: change name 'PageTemplate' throughout this file
require_once './Page.php';

/**
 * This is a template for top level classes, which represent
 * a complete web page and which are called directly by the user.
 * Usually there will only be a single instance of such a class.
 * The name of the template is supposed
 * to be replaced by the name of the specific HTML page e.g. baker.
 * The order of methods might correspond to the order of thinking
 * during implementation.
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 */
class Bakery2 extends Page
{
    protected function __construct()
    {
        parent::__construct();
    }
    public function __destruct()
    {
        parent::__destruct();
    }
    protected function getViewData(): array
    {

        $orders = array();
        $sql = "select * from ordered_article";
        $name = $this->_database->query($sql);
        if (!$name) {
            throw new Exception("Fehler beim Lesen von " + $this->_database->error);
        }
        while ($pizza = $name->fetch_assoc()) {
            $sql2= "select name from article where article_id = {$pizza['article_id']}";
            $name2 = $this->_database->query($sql2);
            $pizza['name']= $name2->fetch_assoc()['name'];
            $orders[] = $pizza;
            if (!$pizza) throw new Exception("Fehler in Abfrage: " . $this->_database->error);

        }
        $name->free();
        $name2->free();
        return $orders;
    }
    protected function generateView(): void
    {
        $data = $this->getViewData();
        $this->generatePageHeader("Bakery");  
        echo <<< HTML
            <h1>Backstube</h1>

            <form action = "Bakery2.php" method ="POST" accept-charset="utf-8">
         HTML;
         $status = array(0 => "", 1 => "", 2 => "", 3 => "", 4 => "");

        foreach ($data as $order) {
            $status[0] = "";
            $status[1] = "";
            $status[2] = "";
            $status[3] = "";
            $status[4] = "";
            $status[$order['status']] = "checked";
            
            if($status[3] == "" && $status[4] == "" ){
                echo <<<HTML
                <section>
                <h2> Bestellung $order[ordered_article_id] : Pizza: $order[name] </h2>
                <p>Bestellt <input type ="radio" id="ordered"  name =$order[ordered_article_id] value= "{$order['ordered_article_id']}|0" $status[0]></p>
                <p>Im Ofen <input type= "radio" id = "in_progress"  name=$order[ordered_article_id] value= "{$order['ordered_article_id']}|1" $status[1]> </p>
                <p>Fertig <input type= "radio" id = "done"  name=$order[ordered_article_id] value="{$order['ordered_article_id']}|2"  $status[2]> </p>
                </section>

            HTML;
            }
            

            
        }
        echo <<< HTML
            <input type="submit" id="submit" value="Bestaetigen">
            </form>
            HTML;
            $this->generatePageFooter();

        $this->generatePageFooter();
    }
    protected function processReceivedData(): void
    {
        if (isset($_POST)) {
           
            foreach ($_POST as $order) {
                $idAndStatus = explode('|', $order);
                //if(substr($order,0,1)=='|'){
                $sql = "UPDATE ordered_article SET status=$idAndStatus[1] WHERE ordered_article_id=$idAndStatus[0]";
                $name = $this->_database->query($sql);
                if (!$name) throw new Exception("Fehler in Abfrage: " . $this->database->error);
                //}
                //header('Location:Order.php');
            }
        }
    }
   
    public static function main(): void
    {
        try {
            $page = new Bakery2();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content type: text/html; charset= UTF-8");
            echo $e->getMessage();
        }
    }
}
Bakery2::main();
