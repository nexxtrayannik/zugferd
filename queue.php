<?php

class InvoiceQueue {

    private $queueFile = 'invoice_queue.json';

    public function addToQueue($invoiceData) {
        $queue = $this->getQueue();
        $queue[] = $invoiceData;
        file_put_contents($this->queueFile, json_encode($queue));
    }

    public function getNextFromQueue() {
        $queue = $this->getQueue();
        if (empty($queue)) {
            return null;
        }
        $invoiceData = array_shift($queue);
        file_put_contents($this->queueFile, json_encode($queue));
        return $invoiceData;
    }

    private function getQueue() {
        if (!file_exists($this->queueFile)) {
            return [];
        }
        $queue = json_decode(file_get_contents($this->queueFile), true);
        return $queue ?: [];
    }
}
