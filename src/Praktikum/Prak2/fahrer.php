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
class Fahrer extends Page
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
        $sql = "select * from ordering";
        $name = $this->_database->query($sql);
        if (!$name) {
            throw new Exception("Fehler beim Lesen von " + $this->_database->error);
        }
        while ($pizza = $name->fetch_assoc()) {
            $sql2 = "SELECT status FROM ordered_article WHERE ordering_id={$pizza['ordering_id']}";
            $status = $this->_database->query($sql2);
            $pizza['status'] = $status->fetch_assoc()['status'];
            //echo ("<pre>");
            //echo var_dump($pizza);
            //echo ("</pre>");
            $orders[] = $pizza;
        }
        $name->free();
        $status->free();
        return $orders;
    }
    protected function generateView(): void
    {
        $status = array(0 => "", 1 => "", 2 => "", 3 => "", 4 => "");
        $data = $this->getViewData();
        $this->generatePageHeader("Fahrer");

        echo <<<HTML
        <form action="" method="POST" accept-charset="utf-8">        
        HTML;
        foreach ($data as $row) {
            $status[0] = "";
            $status[1] = "";
            $status[2] = "";
            $status[3] = "";
            $status[4] = "";
            $status[$row['status']] = "selected";
            if ($status[0] == "" && $status[1] == "" && $status[4] != "selected") {
                echo <<<HTML
                <h2> Bestellung $row[ordering_id]</h2>;
                <p> Addresse: $row[address]</p>;
                <p> Zeit $row[ordering_time]</p>;
                <p> Status <select name = "Status{$row['ordering_id']}" id="status">
                    <option value = "{$row['ordering_id']}|2" $status[2]>Fertig</option>
                    <option value = "{$row['ordering_id']}|3" $status[3]>Unterwegs</option>
                    <option value = "{$row['ordering_id']}|4" $status[4]>Ausgeliefert</option>
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
    protected function processReceivedData(): void
    {
        if (isset($_POST)) {
            //echo "<pre>";
            //echo var_dump($_POST);
            //echo "</pre>";
            foreach ($_POST as $order) {
                echo var_dump($order);
                $idAndStatus = explode('|', $order);
                $sql = "update ordered_article set status= $idAndStatus[1] where ordering_id= $idAndStatus[0] ";
                $name = $this->_database->query($sql);
                if (!$name) {
                    throw new Exception("Fehler beim Lesen von " + $this->_database->error);
                }
            }
        }
    }
    public static function main(): void
    {
        try {
            $page = new Fahrer();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content type: text/html; charset= UTF-8");
            echo $e->getMessage();
        }
    }
}
Fahrer::main();
