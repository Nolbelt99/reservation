<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\PaymentItem;
use App\Entity\Reservation;
use App\Entity\Transaction;
use App\Form\UserRegistType;
use App\Enum\ServiceTypeEnum;
use App\Entity\ReservationItem;
use App\Enum\TransactionTypeEnum;
use Symfony\Component\Mime\Email;
use App\Enum\ReservationStatusEnum;
use App\Enum\TransactionStatusEnum;
use App\Service\ReservationManager;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\ByteString;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/{_locale}", requirements={"_locale": "hu|en|de|"}, name="portal_page_")
 */
class DefaultController extends BaseController
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ){
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/locale", name="locale")
     */
    public function setLocale($_locale, Request $request) {
        $request->setLocale($_locale);
        return $this->redirectToRoute('portal_page_default');
    }

    /**
     * @Route("/", name="default")
     */
    public function default(ServiceRepository $repository, TranslatableListener $translatableListener, Request $request)
    {
        $_locale = $request->getLocale();

        $translatableListener->setTranslatableLocale($_locale);
        $entities = $repository->findForHomepage($_locale, ServiceTypeEnum::APARTMENT);
        $entities = array_merge($repository->findForHomepage($_locale, ServiceTypeEnum::SHIP), $entities);

        return $this->render('portal/default/dashboard.html.twig', [
            'entities' => $entities,
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(Request $request)
    {

        return $this->render('portal/default/contact.html.twig', [
        ]);
    }

    /**
     * @Route("/booking", name="booking")
     */
    public function booking(
        Request $request,
        ReservationManager $manager,
        UserPasswordHasherInterface $passwordEncoder,
        MailerInterface $mailer,
        SessionInterface $session,
        TranslatableListener $translatableListener
    ){
        $_locale = $request->getLocale();
        $translatableListener->setTranslatableLocale($_locale);

        $reservation = $manager->getReservation();
        if (!$reservation->getUser() && ($user = $this->getUser())) {
            $reservation->setUser($user);
            $this->entityManager->persist($reservation);
            $this->entityManager->flush();
            $session->clear();
        }
        $user = new User();
        $form = $this->createForm(UserRegistType::class, $user, ['locale' => $request->getLocale()]);
        $_locale = $request->getLocale();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $oldUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $form->get('email')->getData()]);
                if ($oldUser) {
                    $user = $oldUser;
                    $user->setUsedPassword(false);
                    $user->setUpdatedAt(new DateTime());
                } else {
                    $user->setEmail($form->get('email')->getData());
                    $user->setCreatedAt(new DateTime());
                    $user->setRoles([]);
                }
                $password = strtoupper(ByteString::fromRandom(6)->toString());
                $user->setPassword($passwordEncoder->hashPassword(
                    $user,
                    $password
                ));
                $user->setPasswordAvaibleUntil(new DateTime('+ 1 hour'));
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $email = (new Email())
                    ->from($this->getParameter('mailer_from_address'))
                    ->to($form->get('email')->getData())
                    ->subject('Sikeres regisztráció')
                    ->html('
                        <p>Kedves Felhasználó</p>
                        <p>Ezzel a jelszóval tudja azonosítani magát: ' . $password .'</p>
                    ')
                ;
                $mailer->send($email);

                $session->set('email', $form->get('email')->getData());

                $this->addFlash(
                    'notice',
                    'A bejelentkezéshez egyszer használatos jelszót küldtünk a alábbi címre: ' . $form->get('email')->getData()
                );

                return $this->redirectToRoute('portal_security_login');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Sikertelen regisztráció.');
            }
        }

        return $this->render('portal/default/booking.html.twig', [
            'reservation' => $reservation,
            '_locale' => $_locale,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/sikeres-fizetes/{id}", name="arfter_payment")
     * @IsGranted("ROLE_USER")
     */
    public function payment(Request $request, int $id)
    {
        $payment_succes = false;
        $message = null;

        if (isset($_REQUEST['r']) && isset($_REQUEST['s'])) {

            $code = $request->query->get('r');
            $json = base64_decode($code);
            $array = json_decode($json);
            if($array){
                $transaction = $this->entityManager->getRepository(Transaction::class)->findOneNotFailedByTransaction($array->t);
                $paymentItem = $transaction->getPaymentItem();
                $reservationId = null;
                if ($array->e == 'SUCCESS' && $transaction->getCreatedAt() > new DateTime('-10 mins')) {
                    if ($this->getParameter('app.env') == 'dev') {
                        $transaction->setStatus(TransactionStatusEnum::IPNCHECKED);
                        $paymentItem->setPaid(true);
                        $items = $paymentItem->getReservationItems();
                        foreach ($items as $item) {
                            if ($paymentItem->getType() == TransactionTypeEnum::PAYMENT) {
                                $item->setReservationPaidsuccesfully(true);
                            } else {
                                $item->setAssurancePaidSuccesfully(true);
                            }
                            $this->entityManager->persist($item);
                        }
                        $reservation = $this->entityManager->getRepository(Reservation::class)->findOneBy(['id' => $id]);

                        $this->entityManager->persist($reservation);
                        $this->entityManager->persist($transaction);
                        $this->entityManager->persist($paymentItem);
                        $this->entityManager->flush();
                        if (empty($this->entityManager->getRepository(PaymentItem::class)->findRemainingPaymentItem($reservation, TransactionTypeEnum::PAYMENT))) {
                            $reservation->setReservationStatus(ReservationStatusEnum::PAID_RESERVATION);
                        }

                    }
                    $message = 'Sikeres tranzakció.' . '<br>' . 'SimplePay tranzakció azonosító: ' . $array->t;
                    $payment_succes = true;

                    if (!empty($this->entityManager->getRepository(PaymentItem::class)->findRemainingPaymentItem($reservation, TransactionTypeEnum::PAYMENT))) {
                        $reservationId = $reservation->getId();
                    } elseif(!empty($locks = $this->entityManager->getRepository(PaymentItem::class)->findRemainingPaymentItem($reservation, TransactionTypeEnum::LOCK))) {
                        foreach ($locks as $lock) {
                            if(!empty($reservationItem = $this->entityManager->getRepository(ReservationItem::class)->findItemToLock($lock))){
                                $reservationId = $reservation->getId();
                                break;
                            }
                        }
                    }
                }elseif ($array->e == 'FAIL') {
                    $transaction->setStatus(TransactionStatusEnum::FAILED);
                    $transaction->setFailMessage('Sikertelen fizetés');
                    $this->entityManager->persist($transaction);
                    $message = 'Sikertelen tranzakció.' . '<br>' . 'SimplePay tranzakció azonosító: ' . $array->t . '<br>' . 'Kérjük, ellenőrizze a tranzakció során megadott adatok helyességét. Amennyiben minden adatot helyesen adott meg, a visszautasítás okának kivizsgálása érdekében kérjük, szíveskedjen kapcsolatba lépni kártyakibocsátó bankjával.';
                }elseif ($array->e == 'CANCEL') {
                    $transaction->setStatus(TransactionStatusEnum::FAILED);
                    $transaction->setFailMessage('Megszakított fizetés');
                    $this->entityManager->persist($transaction);
                    $message = 'Megszakított fizetés.';
                }elseif ($array->e == 'TIMEOUT') {
                    $transaction->setStatus(TransactionStatusEnum::FAILED);
                    $transaction->setFailMessage('Időtúllépés');
                    $this->entityManager->persist($transaction);
                    $message = 'Időtúllépés.';
                }
                $this->entityManager->flush();
            }

            return $this->render('portal/payment/after_payment.html.twig', [
                'payment_succes' => $payment_succes,
                'message' => $message,
                'user' => $this->getUser(),
                'reservationId' => $reservationId
            ]);
        }
    }

    /**
     * @Route("/reservation/{id}", requirements={"id": "\d+"}, name="reservation")
     */
    public function reservation(
        Request $request,
        ReservationManager $manager,
        TranslatableListener $translatableListener,
        int $id
    ){
        $_locale = $request->getLocale();
        $translatableListener->setTranslatableLocale($_locale);
        $entities = $this->entityManager->getRepository(ReservationItem::class)->findAllByReservationId($id);

        return $this->render('portal/reservation/show.html.twig', [
            'entities' => $entities,
            '_locale' => $_locale
        ]);
    }
}
