<?php

namespace App\SzamlaAgent\Document\Invoice;

use App\SzamlaAgent\Header\FinalInvoiceHeader;

/**
 * Végszámla kiállításához használható segédosztály
 *
 * @package App\SzamlaAgent\document\invoice
 */
class FinalInvoice extends Invoice {

    /**
     * Végszámla létrehozása
     *
     * @param int $type végszámla típusa (papír vagy e-számla), alapértelmezett a papír alapú számla
     *
     * @throws \App\SzamlaAgent\SzamlaAgentException
     */
    function __construct($type = self::INVOICE_TYPE_P_INVOICE) {
        parent::__construct(null);
        // Alapértelmezett fejléc adatok hozzáadása
        $this->setHeader(new FinalInvoiceHeader($type));
    }
 }