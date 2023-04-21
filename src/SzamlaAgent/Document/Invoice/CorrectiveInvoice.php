<?php

namespace App\SzamlaAgent\Document\Invoice;

use App\SzamlaAgent\Header\CorrectiveInvoiceHeader;

/**
 * Helyesbítő számla kiállításához használható segédosztály
 *
 * @package App\SzamlaAgent\document
 */
class CorrectiveInvoice extends Invoice {

    /**
     * Helyesbítő számla létrehozása
     *
     * @param int $type számla típusa (papír vagy e-számla), alapértelmezett a papír alapú számla
     *
     * @throws \App\SzamlaAgent\SzamlaAgentException
     */
    function __construct($type = self::INVOICE_TYPE_P_INVOICE) {
        parent::__construct(null);
        // Alapértelmezett fejléc adatok hozzáadása
        $this->setHeader(new CorrectiveInvoiceHeader($type));
    }
 }