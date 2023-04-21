<?php

namespace App\SzamlaAgent\Header;

use App\SzamlaAgent\Document\Invoice\Invoice;

/**
 * Helyesbítő számla fejléc
 *
 * @package App\SzamlaAgent\Header
 */
class CorrectiveInvoiceHeader extends InvoiceHeader {

    /**
     * @param int $type
     *
     * @throws \App\SzamlaAgent\SzamlaAgentException
     */
    function __construct($type = Invoice::INVOICE_TYPE_P_INVOICE) {
        parent::__construct($type);
        $this->setCorrective(true);
    }
}