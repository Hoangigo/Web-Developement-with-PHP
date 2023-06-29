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
class bestellung extends Page
{
    // to do: declare reference variables for members 
    // representing substructures/blocks

    /**
     * Instantiates members (to be defined above).
     * Calls the constructor of the parent i.e. page class.
     * So, the database connection is established.
     * @throws Exception
     */
    protected function __construct()
    {
        parent::__construct();
        // to do: instantiate members representing substructures/blocks
    }

    /**
     * Cleans up whatever is needed.
     * Calls the destructor of the parent i.e. page class.
     * So, the database connection is closed.
     */
    public function __destruct()
    {
        parent::__destruct();
        //session_destroy();
    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is returned in an array e.g. as associative array.
     * @return array An array containing the requested data. 
     * This may be a normal array, an empty array or an associative array.
     */
    protected function getViewData(): array
    {
        $articles = array();
        $sql = "SELECT * FROM article";
        $name = $this->_database->query($sql);

        if (!$name) throw new Exception("Fehler in der Abfrage : " . $this->_database->error);

        while ($pizza = $name->fetch_assoc()) {
            $articles[] = $pizza;
        }
        $name->free();
        array_walk_recursive($articles, array($this, "arrayHtmlSpecialChars"));
        return $articles;
    }

    /**
     * First the required data is fetched and then the HTML is
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if available- the content of
     * all views contained is generated.
     * Finally, the footer is added.
     * @return void
     */
    

    protected function generateView():void
    {
        $data = $this->getViewData(); // NOSONAR ignore unused $data
        $this->generatePageHeader('Bestellung');
        echo<<<HTML
        <h1>Bestellung</h1>
        <h2>Speisekarte</h2>
        <section>
        HTML;

        foreach($data as $pizza){
            echo<<<HTML
            <img src="assets/pizza.png" alt="pizza" width="100" height="100" onclick="cart.addItem($pizza[article_id], $pizza[price], '$pizza[name]')">
            <p>$pizza[name] </p>
            <p>$pizza[price] € </p>
            HTML;
        }

        echo<<<HTML
        </section>
        <h2>Warenkorb</h2>
        <form action="" method="POST" accept-charset="utf-8" id="formular">
            <section>
                <select name="Warenkorb[]" size="6" multiple tabindex="1" id="idCart">
                </select>
                <p><span id="idTotalPrice">0.00</span>€</p>
                <input type="text" id="address" name="address" placeholder="Ihre Adresse" value="" required />
            </section>
            <input type="button" id="Delete" value="Alle löschen" onclick="cart.deleteAll()"/>
            <input type="button" id="DeleteOne" value="Auswahl löschen" onclick="cart.deleteSelectedItems()"/>
            <input type="button" id="order" value="Bestellen" onclick="submitCart()"/>
        </form>
        HTML;
        // to do: output view of this page using $data
        $this->generatePageFooter();
    }

    /**
     * Processes the data that comes via GET or POST.
     * If this page is supposed to do something with submitted
     * data do it here.
     * @return void
     */
    protected function processReceivedData(): void
    {
        parent::processReceivedData();
        if (isset($_POST["address"]) && isset($_POST["Warenkorb"])) {
            //echo "<pre>";
            //echo var_dump($_POST);
            //echo "</pre>";
            $adresse_safe = $this->_database->real_escape_string($_POST["address"]);
            $sql = "INSERT INTO ordering (address) VALUES ('" . $adresse_safe . "')";
            $name = $this->_database->query($sql);
            if (!$name) throw new Exception("Fehler bei Insert: " . $this->_database->error);
            $_SESSION["ID"] = $this->_database->insert_id;

            $sql1 = "SELECT ordering_id FROM ordering WHERE address ='" . $adresse_safe . "'";
            $order = $this->_database->query($sql1);
            $ordId = $order->fetch_assoc();
            if (!$ordId) throw new Exception("Fehler in Abfrage: " . $this->_database->error);

            foreach ($_POST["Warenkorb"] as $temp) {
                $temp_safe = $this->_database->real_escape_string($temp);
                $sql3 = "INSERT INTO ordered_article (ordering_id, article_id, status) VALUES ($ordId[ordering_id],$temp_safe,'0')";
                $name = $this->_database->query($sql3);
                if (!$name) throw new Exception("Fehler bei Insert: " . $this->_database->error);
            }
            $order->free();
            header('Location:bestellung.php');
        }
    }

    /**
     * This main-function has the only purpose to create an instance
     * of the class and to get all the things going.
     * I.e. the operations of the class are called to produce
     * the output of the HTML-file.
     * The name "main" is no keyword for php. It is just used to
     * indicate that function as the central starting point.
     * To make it simpler this is a static function. That is you can simply
     * call it without first creating an instance of the class.
     * @return void
     */
    public static function main(): void
    {
        try {
            $page = new bestellung();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            //header("Content-type: text/plain; charset=UTF-8");
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page. 
// That is input is processed and output is created.
bestellung::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >