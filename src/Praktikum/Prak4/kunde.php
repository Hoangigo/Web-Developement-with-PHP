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
class kunde extends Page
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
        $orders = array();
        if (isset($_SESSION["ID"])) {
            $sql = "SELECT * FROM ordered_article WHERE ordering_id = $_SESSION[ID]";
            $name = $this->_database->query($sql);
            if (!$name) throw new Exception("Fehler in Abfrage: " . $this->database->error);
            while ($order = $name->fetch_assoc()) {
                $sql2 = "SELECT name FROM article WHERE article_id={$order['article_id']}";
                $status = $this->_database->query($sql2);
                $order['name'] = $status->fetch_assoc()['name'];
                if (!$order) throw new Exception("Fehler in Abfrage: " . $this->database->error);
                $orders[] = $order;
            }
            $status->free();
            $name->free();
        }
        array_walk_recursive($orders,array($this, "arrayHtmlSpecialChars"));
        return $orders;
    }

    /**
     * First the required data is fetched and then the HTML is
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if available- the content of
     * all views contained is generated.
     * Finally, the footer is added.
     * @return void
     */
    protected function generateView(): void
    {
        echo <<<HTML
        <script src="StatusUpdate.js"></script>
        HTML;
        $data = $this->getViewData(); // NOSONAR ignore unused $data
        
        $this->generatePageHeader('Kunde');
        // to do: output view of this page using $data
        // html output for every parameter from $orders
        /*
        echo "<pre>";
        echo var_dump($_SESSION["ID"]);
        echo "</pre>";
        */
        
        $status = array(0 => "", 1 => "", 2 => "", 3 => "", 4 => "");

        if (isset($_SESSION["ID"])) {
            foreach ($data as $row) {
                //set status to zero / "NULL"        
                $status[0] = "";
                $status[1] = "";
                $status[2] = "";
                $status[3] = "";
                $status[4] = "";
                //set status to parameter from aray
                $status[$row['status']] = "checked";

                //html output
                echo <<<HTML
        <section>
            <h2>Bestellung  $row[ordered_article_id] : Pizza: $row[name]</h2>
            <p>Bestellt  <input type="radio" id="ordered|$row[ordered_article_id]"          disabled   name = " $row[ordered_article_id]"  value="Bestellt"  $status[0]> </p>
            <p>Im Ofen   <input type="radio" id="in_progress|$row[ordered_article_id]"      disabled   name = " $row[ordered_article_id]"  value="Im Ofen"   $status[1]> </p>
            <p>Fertig    <input type="radio" id="ready_to_deliver|$row[ordered_article_id]" disabled   name = " $row[ordered_article_id]"  value="Fertig"    $status[2]> </p>
            <p>Unterwegs <input type="radio" id="on_road|$row[ordered_article_id]"          disabled   name = " $row[ordered_article_id]"  value="Unterwegs" $status[3]> </p>
            <p>Geliefert <input type="radio" id="delivered|$row[ordered_article_id]"        disabled   name = " $row[ordered_article_id]"  value="Geliefert" $status[4]> </p>
            </section> 
        HTML;
            }
        } else {
            echo "<h1>Noch keine Bestellungen vorhanden!</h1>";
        }
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
        // to do: call processReceivedData() for all members
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
            $page = new kunde();
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
kunde::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >