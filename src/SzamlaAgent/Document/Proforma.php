<?php

namespace App\SzamlaAgent\Document;

use App\SzamlaAgent\Document\Invoice\Invoice;
use App\SzamlaAgent\Header\ProformaHeader;

/**
 * Díjbekérő segédosztály
 *
 * @package App\SzamlaAgent\document
 */
class Proforma extends Invoice {

    /**
     * Díjbekérő számlaszám alapján
     */
    const FROM_INVOICE_NUMBER = 1;

    /**
     * Díjbekérő rendelésszám alapján
     */
    const FROM_ORDER_NUMBER = 2;

    /**
     * Díjbekérő létrehozása
     *
     * @throws \Exception
     */
    function __construct() {
        parent::__construct(null);
        // Alapértelmezett fejléc adatok hozzáadása a díjbekérőhöz
        $this->setHeader(new ProformaHeader());
    }
 }