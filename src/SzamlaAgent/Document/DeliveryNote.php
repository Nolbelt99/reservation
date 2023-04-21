<?php

namespace App\SzamlaAgent\Document;

use App\SzamlaAgent\Document\Invoice\Invoice;
use App\SzamlaAgent\Header\DeliveryNoteHeader;

/**
 * Szállítólevél segédosztály
 *
 * @package szamlaagent\document
 */
class DeliveryNote extends Invoice {

    /**
     * Szállítólevél kiállítása
     *
     * @throws \Exception
     */
    function __construct() {
        parent::__construct(null);
        // Alapértelmezett fejléc adatok hozzáadása
        $this->setHeader(new DeliveryNoteHeader());
    }
 }