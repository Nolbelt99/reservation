<?php

namespace App\SzamlaAgent\Header;

use App\SzamlaAgent\Document\Invoice\Invoice;

/**
 * Előlegszámla fejléc
 *
 * @package App\SzamlaAgent\Header
 */
class PrePaymentInvoiceHeader extends InvoiceHeader {

    /**
     * @param int $type
     *
     * @throws \App\SzamlaAgent\SzamlaAgentException
     */
    function __construct($type = Invoice::INVOICE_TYPE_P_INVOICE) {
        parent::__construct($type);
        $this->setPrePayment(true);
        $this->setPaid(false);
    }
}