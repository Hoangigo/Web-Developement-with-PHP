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
class Order extends Page
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
        $articles = array();
        $sql = "select * from article";
        $name = $this->_database->query($sql);
        if (!$name) {
            throw new Exception("Fehler beim Lesen von " + $this->_database->error);
        }
        while ($pizza = $name->fetch_assoc()) {
            $articles[] = $pizza;
        }
        $name->free();
        return $articles;
    }
    protected function generateView(): void
    {
        $endpreis = 0;
        $data = $this->getViewData();
        $this->generatePageHeader("Bestellung");
        echo <<< HTML
            <h1>Bestellung</h1>
        HTML;
        /*
        $sqltemp = "select * from ordering";
        $nametemp = $this->_database->query($sqltemp);
        if (!$nametemp) throw new Exception("Fehler beim Insert " + $this->_database->error);
        foreach ($nametemp as $temp) {
            echo <<< HTML
            <p>   $temp[address]     </p>
            HTML;
        }
        */

        foreach ($data as $pizza) {
            echo <<< HTML
            <img src = "photos/{$pizza['picture']}" alt="" width="100" height="100">
            <p> Art: {$pizza['name']}</p>
            <p> Preis: {$pizza['price']}</p>
            HTML;
        }

        //close section ? database? 
        echo <<< HTML
        <h2>Warenkorb</h2>
        <form action = "order.php" method ="POST" accept-charset="utf-8">
        <section>
            <select name= "Warenkorb[]" size="6" multiple tabindex ="1">
            <option selected value="1">Salami</option>
            <option value="2">Vegetaria</option>
            <option value="3">Spinat-Huenchen</option>
            <input type="text" name="address" placeholder="Ihre Adresse" required>
        </section> 
        <p>$endpreis</p>
        <input type="button" id="deleteAll" value="Alles Löschen">
        <input type="button" id="delete" value="Auswahl Löschen">
        <input type="submit" id="order" value="Bestellen">
    </form>
    HTML;
        $this->generatePageFooter();
    }
    protected function processReceivedData(): void
    {

        parent::processReceivedData();
        if (isset($_POST["address"]) && isset($_POST["Warenkorb"])) {
            $sql = "INSERT INTO ordering(address) VALUES ('" . $_POST["address"] . "')";
            $name = $this->_database->query($sql);
            if (!$name) throw new Exception("Fehler beim Insert " + $this->_database->error);
            $sql1 = "SELECT ordering_id from ordering where address = '" . $_POST["address"] . "'";
            $order = $this->_database->query($sql1);
            if (!$order) throw new Exception("Fehler in der Abfrage " + $this->_database->error);
            $orderid = $order->fetch_assoc();
            if (!$orderid) throw new Exception("Fehler in der Abfrage " + $this->_database->error);
            foreach ($_POST["Warenkorb"] as $temp) {
                $sql3 = "INSERT INTO ordered_article(ordering_id, article_id, status) VALUES ($orderid[ordering_id],$temp,'0')";
                $name = $this->_database->query($sql3);
                if (!$name) throw new Exception("Fehler beim Insert " + $this->_database->error);
            }
            $order->free();
            //header('Location:Order.php');
        }
    }
    public static function main(): void
    {
        try {
            $page = new Order();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content type: text/html; charset= UTF-8");
            echo $e->getMessage();
        }
    }
}
order::main();
