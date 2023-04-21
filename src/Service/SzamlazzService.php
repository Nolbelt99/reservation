<?php

namespace App\Service;

use DateTime;
use App\SzamlaAgent\Log;
use App\SzamlaAgent\Buyer;
use App\SzamlaAgent\SzamlaAgentAPI;
use App\SzamlaAgent\SzamlaAgentUtil;
use App\SzamlaAgent\Item\InvoiceItem;
use App\SzamlaAgent\Document\Document;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\TranslatableListener;
use App\SzamlaAgent\Document\Invoice\Invoice;
use App\SzamlaAgent\CreditNote\InvoiceCreditNote;
use App\SzamlaAgent\Response\SzamlaAgentResponse;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SzamlazzService
{
    private TranslatableListener $translatableListener;
    private ParameterBagInterface $param;


    public function __construct(TranslatableListener $translatableListener, ParameterBagInterface $param)
    {
        $this->translatableListener = $translatableListener;
        $this->param = $param;
        $this->translatableListener->setTranslatableLocale($this->param->get('locale'));
    }

    public function crateInvoive($user, $transaction, $agentApiKey): string
    {
        try {
            $agent = SzamlaAgentAPI::create($agentApiKey, false, Log::LOG_LEVEL_ERROR);
        
            $invoice = new Invoice(Invoice::INVOICE_TYPE_E_INVOICE);

            $header = $invoice->getHeader();
            $header->setInvoiceTemplate(Invoice::INVOICE_TEMPLATE_DEFAULT);
        
            // Számla fizetési módja (bankkártya)
            $header->setPaymentMethod(Invoice::PAYMENT_METHOD_BANKCARD);
            $header->setPaymentDue($transaction->getCreatedAt()->format('Y-m-d'));

            $header->setComment('Fizetési tranzakció: '. $transaction->getTransactionId() . "
            Felhasználói azonostó: " . $user->getEmail());

            // Vevő adatainak hozzáadása (kötelezően kitöltendő adatokkal)
            $buyer = new Buyer($user->getInvoiceAddressName(), $user->getInvoiceAddressZip(),  $user->getInvoiceAddressCity(),  $user->getInvoiceAddressStreetAndOther());

            $invoice->setBuyer($buyer);

            foreach ($transaction->getPaymentItem()->getReservationItems() as $reservationItem) {
                $grossPrice = $reservationItem->getReservationPrice();
                $netPrice = $grossPrice / 1.27;
                $netPrice = round($netPrice, 1);
                $vat = $netPrice * 0.27;
                $vat = round($vat, 1);
    
                // Számla tétel összeállítása alapértelmezett adatokkal (1 db tétel 27%-os ÁFA tartalommal)
                $item = new InvoiceItem($reservationItem->getService()->getName(), $netPrice);
                // Tétel nettó értéke
                $item->setNetPrice($netPrice);
                // Tétel ÁFA értéke
                $item->setVatAmount($vat);
                // Tétel bruttó értéke
                $item->setGrossAmount($grossPrice);
                // Tétel hozzáadása a számlához
                $invoice->addItem($item);
            }

            // Számla elkészítése
            $result = $agent->generateInvoice($invoice);

            // Agent válasz sikerességének ellenőrzése
            if ($result->isSuccess()) {
                return  $result->getResponse()['headers']['szlahu_szamlaszam'];
            } else {
                return 'Hiba';
            }
        } catch (\Exception $e) {
            return 'Hiba';
        }
    }

    public function download($invoiceNumber, $agentApiKey)
    {
        try {
            $agent = SzamlaAgentAPI::create($agentApiKey, false, Log::LOG_LEVEL_ERROR);
            $agent->setResponseType(SzamlaAgentResponse::RESULT_AS_XML);
        
            $agent->getInvoicePdf($invoiceNumber);
        
            return 'Succes';

        } catch (\Exception $e) {
            return 'Hiba';
        }
    }

    public function setInvoicePaid($invoiceNumber, $price, $agentApiKey)
    {
        try {
            // Számla Agent létrehozása alapértelmezett adatokkal
            $agent = SzamlaAgentAPI::create($agentApiKey, false, Log::LOG_LEVEL_ERROR);

            // Új számla létrehozása
            $invoice_paid = new Invoice(Invoice::INVOICE_TYPE_E_INVOICE);

            // Számla fejléce
            $header = $invoice_paid->getHeader();

            // Annak a számlának a számlaszáma, amelyikhez a jóváírást szeretnénk rögzíteni
            $header->setInvoiceNumber($invoiceNumber);

            // Fejléc hozzáadása a számlához
            $invoice_paid->setHeader($header);
        
            // Hozzáadjuk a jóváírás összegét (false esetén felülírjuk a teljes összeget)
            $invoice_paid->setAdditive(true);
        
            // Új jóváírás létrehozása (az összeget a számla devizanemében kell megadni)
            $creditNote = new InvoiceCreditNote(SzamlaAgentUtil::getTodayStr(), $price, Document::PAYMENT_METHOD_BANKCARD, 'E-V');

            // Jóváírás hozzáadása a számlához
            $invoice_paid->addCreditNote($creditNote);

            // Számla jóváírás elküldése
            $result = $agent->payInvoice($invoice_paid);

            // Agent válasz sikerességének ellenőrzése
            if ($result->isSuccess()) {
                return 'Succes';
            }
        } catch (\Exception $e) {
            return 'Hiba';
        }
    }  
}