<?php

require 'zugferd.php';
require 'queue.php';

use ZUGFeRD\ZUGFeRDGenerator;

class InvoiceQueueWorker {

    private $queue;

    public function __construct() {
        $this->queue = new InvoiceQueue();
    }

    public function processQueue() {
        while (true) {
            $invoiceData = $this->queue->getNextFromQueue();
            if ($invoiceData) {
                $this->generateXML($invoiceData);
            } else {
                // Warten, bevor erneut geprüft wird
                sleep(5);
            }
        }
    }

    private function generateXML($invoiceData) {
        $generator = new ZUGFeRDGenerator();

        foreach ($invoiceData as $field => $value) {
            $generator->addField($field, $value);
        }

        $xml = $generator->createXML();
        $filename = 'rechnung_' . $invoiceData['fldInvoiceNo'] . '.xml';
        $generator->saveXML($xml, $filename);

        echo "XML-Datei $filename erfolgreich erstellt!\n";
    }
}

// Starten des Queue-Workers
$worker = new InvoiceQueueWorker();
$worker->processQueue();
