<?php

namespace App\SzamlaAgent\Document\Invoice;

use App\SzamlaAgent\Header\ReverseInvoiceHeader;

/**
 * Sztornó számla
 *
 * @package App\SzamlaAgent\document\invoice
 */
class ReverseInvoice extends Invoice {

    /**
     * Sztornó számla létrehozása
     *
     * @param int $type számla típusa (papír vagy e-számla), alapértelmezett a papír alapú számla
     *
     * @throws \App\SzamlaAgent\SzamlaAgentException
     */
    public function __construct($type = self::INVOICE_TYPE_P_INVOICE) {
        parent::__construct(null);
        // Alapértelmezett fejléc adatok hozzáadása a számlához
        if (!empty($type)) {
            $this->setHeader(new ReverseInvoiceHeader($type));
        }
    }
}