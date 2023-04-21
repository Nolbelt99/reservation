<?php

namespace App\SzamlaAgent\Header;

/**
 * Szállítólevél fejléc
 *
 * @package App\SzamlaAgent\Header
 */
class DeliveryNoteHeader extends InvoiceHeader {

    /**
     * @throws \App\SzamlaAgent\SzamlaAgentException
     */
    function __construct() {
        parent::__construct();
        $this->setDeliveryNote(true);
        $this->setPaid(false);
    }
}