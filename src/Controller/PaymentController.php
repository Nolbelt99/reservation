<?php

namespace App\Controller;

use DateTime;
use App\Form\PaymentType;
use App\Entity\PaymentItem;
use App\Entity\Reservation;
use App\Entity\Transaction;
use Psr\Log\LoggerInterface;
use App\Entity\ReservationItem;
use App\Enum\TransactionTypeEnum;
use App\Repository\UserRepository;
use App\Enum\ReservationStatusEnum;
use App\Enum\TransactionStatusEnum;
use Symfony\Component\Intl\Countries;
use Doctrine\ORM\EntityManagerInterface;
use App\SimplePay\Classes\SimplePayStart;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @Route("/{_locale}", requirements={"_locale": "hu|en|de|"})
 * @IsGranted("ROLE_USER")
*/
class PaymentController extends AbstractController
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ){
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/fizetes/{id}/", name="payment")
     */
    public function payment(Request $request, int $id, string $_locale)
    {
        $reservation = $this->entityManager->getRepository(Reservation::class)->findOneById($id, $this->getUser(), ReservationStatusEnum::UNDER_RESERVATION);
        $paidReservation = $this->entityManager->getRepository(Reservation::class)->findOneById($id, $this->getUser(), ReservationStatusEnum::PAID_RESERVATION);

        if ($reservation || $paidReservation) {
            if (!$reservation) {
                $locks = $this->entityManager->getRepository(PaymentItem::class)->findRemainingPaymentItem($paidReservation, TransactionTypeEnum::LOCK);
                if (!empty($locks)) {
                    $in21Days = false;
                    foreach ($locks as $lock) {
                        if(!empty($reservationItem = $this->entityManager->getRepository(ReservationItem::class)->findItemToLock($lock))){
                            $in21Days = true;
                            $reservation = $paidReservation;
                            break;
                        }
                    }
                    if ($in21Days === false) {
                        throw $this->createNotFoundException('Az oldal nem található.');
                    }
                } else {
                    throw $this->createNotFoundException('Az oldal nem található.');
                }
            }
            \Locale::setDefault($_locale);
            $countries = array_change_key_case(Countries::getNames(), CASE_LOWER);
            $countries = array_flip($countries);
            $form = $this->createForm(PaymentType::class, $this->getUser(), ['countries' => $countries]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $entity = $form->getData();
                    foreach ($reservation->getReservationItems() as $item) {
                        $paymentItem = $this->entityManager->getRepository(PaymentItem::class)
                            ->findPaymentItem($reservation, TransactionTypeEnum::PAYMENT, $item->getService()->getCompanyName());
                        if (!$paymentItem) {
                            $paymentItem = new PaymentItem();
                            $paymentItem->setCompanyName($item->getService()->getCompanyName());
                            $paymentItem->setCompanyPriority($item->getService()->getCompanyPriority());
                            $paymentItem->addReservationItem($item);
                            $paymentItem->setReservation($reservation);
                            $paymentItem->setType(TransactionTypeEnum::PAYMENT);
                            $paymentItem->setUser($this->getUser());
                            $paymentItem->setSumPrice($item->getReservationPrice());
                            $this->entityManager->persist($paymentItem);
                            $this->entityManager->flush();
                        } else {
                            $reservationItems = $paymentItem->getReservationItems()->getValues();
                            $newItem = true;
                            foreach ($reservationItems as $value) {
                                if ($item->getId() == $value->getId()) {
                                    $newItem = false;
                                }
                            }
                            if($newItem === true){
                                $paymentItem->addReservationItem($item);
                                $paymentItem->setSumPrice($paymentItem->getSumPrice() + $item->getReservationPrice());
                                $this->entityManager->persist($paymentItem);
                                $this->entityManager->flush();
                            }
                        }
                        if ($item->getService()->getAssurance()) {
                            $paymentLockItem = $this->entityManager->getRepository(PaymentItem::class)
                                ->findByReservationItem($item, TransactionTypeEnum::LOCK, $item->getService()->getCompanyName());
                            
                            if (!$paymentLockItem) {
                                $paymentItem = new PaymentItem();
                                $paymentItem->setCompanyName($item->getService()->getCompanyName());
                                $paymentItem->setCompanyPriority($item->getService()->getCompanyPriority());
                                $paymentItem->addReservationItem($item);
                                $paymentItem->setReservation($reservation);
                                $paymentItem->setSumPrice($item->getPaidAssurance());
                                $paymentItem->setType(TransactionTypeEnum::LOCK);
                                $paymentItem->setUser($this->getUser());
                                $this->entityManager->persist($paymentItem);
                                $this->entityManager->flush();
                            }
                        }
                    }

                    $orderedPaymentItems = $this->entityManager->getRepository(PaymentItem::class)->getOrderedItems($reservation);

                    foreach ($orderedPaymentItems as $paymentItem) {
                        $transaction = $this->entityManager->getRepository(Transaction::class)->findNotPaidByItem($paymentItem);
                        if (!$transaction) {
                            $transaction = new Transaction();
                            $transaction->setStatus(TransactionStatusEnum::UNPAID);
                            $transaction->setCreatedAt(new DateTime());
                            $transaction->setUser($this->getUser());
                            $transaction->setPaymentItem($paymentItem);
                            if ($paymentItem->getType() == TransactionTypeEnum::PAYMENT) {
                                $transaction->setType(TransactionTypeEnum::PAYMENT);
                            } else {
                                $transaction->setType(TransactionTypeEnum::LOCK);
                            }
                            $this->entityManager->persist($transaction);        
                            break;
                        }
                    }

                    $this->entityManager->persist($entity);
                    $this->entityManager->flush();

                    return $this->redirectToRoute('simplepay_start_storage', [
                        'reservation' => $reservation->getId(),
                        'transaction' => $transaction->getId(),
                        '_locale' => 'hu'
                    ]);
    
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Hiba.' . $e->getMessage());
                }
            }
            if ($form->isSubmitted() && !$form->isValid()) {
                $this->addFlash('error', 'Sikertelen rögzítés, érvénytelen értékek.');
            }
    
            return $this->render('portal/payment/form.html.twig', [
                'user' => $this->getUser(),
                'form' => $form->createView(),
            ]);

        }
        throw $this->createNotFoundException('Az oldal nem található.');
    }

    /**
     * @Route("/startstorage", name="simplepay_start_storage")
     */
    public function startStorage(
        Request $request,
        TranslatableListener $translatableListener,
        ParameterBagInterface $param,
        string $_locale
    ) {
        $translatableListener->setTranslatableLocale($param->get('locale'));
        $transaction = $this->entityManager->getRepository(Transaction::class)->findOneBy(['id' => $request->query->get('transaction')]);
        $paymentItem = $transaction->getPaymentItem();
        $items = $paymentItem->getReservationItems();

        require_once $this->getParameter('kernel.project_dir') . '/config/packages/simplepay.php';

        $trx = new SimplePayStart;

        $config = $configs[$paymentItem->getCompanyName()];
        
        $trx->addConfig($config);
        $trx->addData('currency', 'HUF');

        $total = 0;

        foreach ($items as $item) {
            if ($paymentItem->getType() == TransactionTypeEnum::PAYMENT) {
                $total += $item->getReservationPrice();
                $trx->addItems(
                    array(
                        'title' => $item->getService()->getName(),
                        'price' => $item->getReservationPrice(),
                        'amount' => 1,
                    )
                );
            } else {
                //$trx->addData('twoStep', true);

                $total += $item->getPaidAssurance();
                $trx->addItems(
                    array(
                        'title' => $item->getService()->getName() . ' - biztosíték',
                        'price' => $item->getPaidAssurance(),
                        'amount' => 1,
                    )
                );
            }

        }

        $trx->addData('total', $total);

        $trx->addData('orderRef', str_replace(array('.', ':', '/'), "", @$_SERVER['SERVER_ADDR']) . @date("U", time()) . rand(1000, 9999));

        $trx->addData('threeDSReqAuthMethod', '02');

        $trx->addData('customer', $this->getUser()->getFirstName() . ' ' . $this->getUser()->getLastName());
        $trx->addData('customerEmail', $this->getUser()->getEmail());

        $trx->addData('language', strtoupper($_locale));

        $timeoutInSec = 600;
        $timeout = @date("c", time() + $timeoutInSec);
        $trx->addData('timeout', $timeout);

        $trx->addData('methods', array('CARD'));

        $config['URL'] = 'http://' . $_SERVER['HTTP_HOST'] . '/hu/sikeres-fizetes/' . $request->query->get('reservation'); //return url

        $trx->addData('url', $config['URL']);

        $trx->addGroupData('invoice', 'name', $this->getUser()->getInvoiceAddressName());
        $trx->addGroupData('invoice', 'country', $this->getUser()->getInvoiceAddressCountry());
        $trx->addGroupData('invoice', 'city', $this->getUser()->getInvoiceAddressCity());
        $trx->addGroupData('invoice', 'zip', $this->getUser()->getInvoiceAddressZip());
        $trx->addGroupData('invoice', 'address',  $this->getUser()->getInvoiceAddressStreetAndOther());

        $trx->formDetails['element'] = 'auto';

        $trx->runStart();

        $trx->getHtmlForm();

        if ($trx->getReturnData()['form'] != "Transaction start was failed!") {
            $transaction->setTransactionId($trx->getReturnData()['transactionId']);
            $transaction->setStatus(TransactionStatusEnum::INPROGRESS);
        } else {
            $transaction->setTransactionId('');
            $transaction->setStatus(TransactionStatusEnum::FAILED);
        }

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        return $this->render('portal/payment/startstorage.html.twig', [
            'form' => $trx->returnData['form'],
        ]);
    }

}
