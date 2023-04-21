<?php

namespace App\Admin\Controller;

use App\Entity\Contact;
use App\Entity\Reservation;
use App\Entity\Transaction;
use App\Admin\Form\EmailFormType;
use Symfony\Component\Mime\Email;
use App\Admin\Form\ContactFormType;
use App\Admin\Filter\TransactionFilter;
use App\Admin\Controller\BaseController;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdater;


/**
 * @Route("/admin", name="admin_")
 */
class DefaultController extends BaseController
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="dashboard")
     */
    public function dashboard()
    {
        return $this->render('admin/dashboard.html.twig');
    }

    /**
     * @Route("/email-kuldes/{id}", name="send_email")
     */
    public function sendEmail(Request $request, MailerInterface $mailer, int $id)
    {
        $reservation = $this->entityManager->getRepository(Reservation::class)->findOneBy(['id' => $id]);
        if (!$reservation instanceof Reservation) {
            throw $this->createNotFoundException('A foglalás nem található.');
        }
        $form = $this->createForm(EmailFormType::class);
        try {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($form->isValid()) {
                    $email = (new Email())
                        ->from($this->getParameter('mailer_from_address'))
                        ->to($reservation->getUser()->getEmail())
                        ->subject($form->get('subject')->getData())
                        ->html($form->get('body')->getData())
                    ;
                    $mailer->send($email);
                    $this->addFlash('notice', 'Sikeres email küldés.');
                    return $this->redirectToRoute('admin_reservation_form', ['id' => $id]);
                }
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Sikertelen rögzítés.' . $e->getMessage());
        }
        return $this->render('admin/email/form.html.twig', [
            'form' => $form->createView(),
            'entity' => $reservation
        ]);
    }

    /**
     * @Route("/kapcsolat", name="contact")
     */
    public function form(
        Request $request
    ){
        $contact = $this->entityManager->getRepository(Contact::class)->findAll();
        if (empty($contact)) {
            $entity = new Contact();
        } else {
            $entity = $contact[0];
        }

        $form = $this->createForm(ContactFormType::class, $entity);
        try {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($form->isValid()) {
                    $this->entityManager->persist($entity);
                    $this->entityManager->flush();
                    $this->addFlash('notice', 'Sikeres rögzítés.');
                    if ($entity->getId()) {
                        return $this->redirectToRoute('admin_contact', ['id' => $entity->getId()]);
                    }
                } else {
                    $this->addFlash('error', 'Sikertelen rögzítés, érvénytelen értékek.');
                }
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Sikertelen rögzítés.');
        }

        return $this->render('admin/contact/form.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity
        ]);
    }

    /**
     * @Route("/tranzakciók/", name="transactions")
     */
    public function list(
        Request $request,
        PaginatorInterface $paginator,
        FilterBuilderUpdater $filterBuilderUpdater,
        FormFactoryInterface $formFactory
    ){
        $queryBuilder = $this->entityManager->getRepository(Transaction::class)->findListElements();
        $filterForm = $formFactory->create(TransactionFilter::class);
        if ($request->query->has($filterForm->getName())) {
            $filterForm->submit($request->query->get($filterForm->getName()));
            if ($filterForm->isValid()) {
                $filterBuilderUpdater->addFilterConditions($filterForm, $queryBuilder);
            }
        }
        $entities = $this->orderedPaginatedList($request, $queryBuilder, $paginator);
        return $this->render('admin/transaction/list.html.twig', [
            'entities' => $entities,
            'filterForm' => $filterForm->createView(),
        ]);
        
    }
}