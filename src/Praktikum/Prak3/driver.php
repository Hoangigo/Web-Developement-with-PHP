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
class driver extends Page
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
    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is returned in an array e.g. as associative array.
     * @return array An array containing the requested data.
     * This may be a normal array, an empty array or an associative array.
     */
    protected function getViewData(): array
    {
        $deliveries = array();

        $sql = "SELECT * FROM ordering";
        $name = $this->_database->query($sql);

        if (!$name) throw new Exception("Fehler in Abfrage: " . $this->_database->error);
        while ($pizza = $name->fetch_assoc()) {

            $pizza["address"] = $pizza["address"];
            $sql2 = "SELECT status FROM ordered_article WHERE ordering_id={$pizza['ordering_id']}";
            $status = $this->_database->query($sql2);
            $pizza['status'] = $status->fetch_assoc()['status'];
            if (!$pizza) throw new Exception("Fehler in Abfrage: " . $this->_database->error);
            $deliveries[] = $pizza;
        }

        $status->free();
        $name->free();
        array_walk_recursive($deliveries,array($this, "arrayHtmlSpecialChars"));
        return $deliveries;

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
        $status = array(0 => "", 1 => "", 2 => "", 3 => "", 4 => "");
        $data = $this->getViewData(); // NOSONAR ignore unused $data
        $this->generatePageHeader('Fahrer');
        echo <<<HTML
        <form action="" method="POST" accept-charset="utf-8">        
        HTML;
        $hiddenFieldValues = "";
        foreach ($data as $row) {
            //echo "<pre>";
            //var_dump($data);
            //echo "</pre>";
            $status[0] = "";
            $status[1] = "";
            $status[2] = "";
            $status[3] = "";
            $status[4] = "";
            $status[$row['status']] = "selected";
            if ($status[0] == "" && $status[1] == "" && $status[4] == "") {
                // $realTime = htmlspecialchars($row["ordering_time"]);
                // $realId =htmlspecialchars($row["ordering_id"]);
                echo <<<HTML
               <section>
                    <h2>Bestellung $row[ordering_id]</h2>
                    <p> Adresse: $row[address] </p>
                    <p> Preis: $row[address] </p>
                    <p> Zeit: $row[ordering_time] </p>
                    <p>Status  <select name="{$row['ordering_id']}" id="status">            
                    <!-- <option value="{$row['ordering_id']}|0">   Bestellt    </option> -->
                    <!-- <option value="{$row['ordering_id']}|1">   Im Ofen     </option> -->
                    <option value="{$row['ordering_id']}|2" $status[2]>   Fertig      </option>
                    <option value="{$row['ordering_id']}|3" $status[3]>   Unterwegs   </option>
                    <option value="{$row['ordering_id']}|4" $status[4]>   Geliefert   </option>
                    </select> </p>
                </section>   
        HTML;
                $hiddenFieldValues = $hiddenFieldValues . "|" . $row["ordering_id"];
            }
        }
        if($data == ""){
            echo <<<html
            Es gibt keine Bestellungen
            </form>
            html;
        } else {
            $hiddenFieldValues = substr($hiddenFieldValues, 1);
            echo <<<html
            <input type = "hidden" name = "hiddenField" value = "$hiddenFieldValues">
            <input type="submit" id="submit" value="Bestätigen">
            </form>
            html;
        }

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
        $allIDs = "";
        if(isset($_POST["hiddenField"])){
            //var_dump($_POST);
            $allIDs = explode('|', $_POST["hiddenField"]);
            foreach($allIDs as $id){
                if(isset($_POST[$id])){

                    $order = $_POST[$id];
                    $order = $this->_database->real_escape_string($order);
                    $idAndStatus = explode('|', $order);
                    $sql="UPDATE ordered_article SET status=$idAndStatus[1] WHERE ordering_id=$idAndStatus[0]";
                    $name = $this->_database->query($sql);
                    if(!$name) throw new Exception("Fehler in Abfrage: ".$this->database->error);
                }
            }
            //header('Location:driver.php');
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
            $page = new driver();
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
driver::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends).
// Not specifying the closing ? >  helps to prevent accidents
// like additional whitespace which will cause session
// initialization to fail ("headers already sent").
//? >
