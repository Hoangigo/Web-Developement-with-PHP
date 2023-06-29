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
class Customer extends Page
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
        $sql = "select * from ordered_article";
        $name = $this->_database->query($sql);
        if (!$name) {
            throw new Exception("Fehler beim Lesen von " + $this->_database->error);
        }
        while ($order = $name->fetch_assoc()) {
            $sql2 = "select name from article where article_id= {$order['article_id']}";
            $name2 = $this->_database->query($sql2);
            $order['name'] = $name2->fetch_assoc()['name'];
            $articles[] = $order;
            if (!$order) throw new Exception("Fehler in Abfrage: " . $this->_database->error);
        }
        $name->free();
        $name2->free();
        return $articles;
    }
    protected function generateView(): void
    {
        $status = array(0 => "", 1 => "", 2 => "", 3 => "", 4 => "");
        $data = $this->getViewData();
        $this->generatePageHeader("Kunde");
        echo <<< HTML
            <h1>Kunde</h1>
        HTML;

        foreach ($data as $order) {

            $status[0] = "";
            $status[1] = "";
            $status[2] = "";
            $status[3] = "";
            $status[4] = "";
            $status[$order['status']] = "checked";
            //echo "<pre>";
            //var_dump($status);
            //echo "</pre>";
            echo <<<HTML
            <form>
            <h2>Bestellung $order[ordered_article_id] : Pizza $order[name]</h2>
            <p>Bestellt <input type= "radio" id = "ordered" disabled name=$order[ordering_id] value= "Bestellt" $status[0]> </p>
            <p>Im Ofen <input type= "radio" id = "in_progress" disabled name=$order[ordering_id] value= "Im Ofen" $status[1]> </p>
            <p>Fertig <input type= "radio" id = "done" disabled name=$order[ordering_id] value= "Fertig" $status[2]> </p>
            <p>Unterweg <input type= "radio" id = "on_road" disabled name=$order[ordering_id] value= "Unterweg" $status[3]> </p>
            <p>Ausgeliefert <input type= "radio" id = "delivered" disabled name=$order[ordering_id] value= "Ausgeliefert" $status[4]> </p>
            </form>

            HTML;
        }

        $this->generatePageFooter();
    }
    protected function processReceivedData(): void
    {
        parent::processReceivedData();
    }
    public static function main(): void
    {
        try {
            $page = new Customer();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content type: text/html; charset= UTF-8");
            echo $e->getMessage();
        }
    }
}
Customer::main();
