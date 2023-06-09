<?php

namespace App\SzamlaAgent\Document\Receipt;

use App\SzamlaAgent\Header\ReverseReceiptHeader;

/**
 * Sztornó nyugta
 *
 * @package App\SzamlaAgent\document\receipt
 */
class ReverseReceipt extends Receipt {

    /**
     * Sztornó nyugta létrehozása nyugtaszám alapján
     *
     * @param string $receiptNumber
     */
    public function __construct($receiptNumber = '') {
        parent::__construct(null);
        $this->setHeader(new ReverseReceiptHeader($receiptNumber));
    }
}