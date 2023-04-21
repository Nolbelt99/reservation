<?php

namespace App\SzamlaAgent\Header;

use App\SzamlaAgent\Document\Invoice\Invoice;

/**
 * Végszámla fejléc
 *
 * @package App\SzamlaAgent\Header
 */
class FinalInvoiceHeader extends InvoiceHeader {

    /**
     * @param int $type
     *
     * @throws \App\SzamlaAgent\SzamlaAgentException
     */
    function __construct($type = Invoice::INVOICE_TYPE_P_INVOICE) {
        parent::__construct($type);
        $this->setFinal(true);
    }
}