<?php

namespace App\Admin\Controller;

use DateTime;
use App\Entity\Reservation;
use App\Entity\ReservationItem;
use App\Enum\ReservationStatusEnum;
use App\Admin\Filter\ReservationFilter;
use App\Admin\Form\ReservationFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\ValidationErrorException;
use Knp\Component\Pager\PaginatorInterface;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdater;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @Route("/admin/foglalasok", name="admin_reservation_")
 */
class ReservationController extends BaseController
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/list/{op}", name="list")
     */
    public function list(
        Request $request,
        PaginatorInterface $paginator,
        FilterBuilderUpdater $filterBuilderUpdater,
        FormFactoryInterface $formFactory,
        $op = null
    ){
        if ($op && $op != 'manual') {
           $op = null;
        }

        $queryBuilder = $this->entityManager->getRepository(Reservation::class)->findListElements(strtoupper($op));

        $filterForm = $formFactory->create(ReservationFilter::class);
        if ($request->query->has($filterForm->getName())) {
            $filterForm->submit($request->query->get($filterForm->getName()));
            if ($filterForm->isValid()) {
                $filterBuilderUpdater->addFilterConditions($filterForm, $queryBuilder);
            }
        }

        $entities = $this->orderedPaginatedList($request, $queryBuilder, $paginator);
        return $this->render('admin/reservation/list.html.twig', [
            'op' => $op,
            'entities' => $entities,
            'filterForm' => $filterForm->createView(),
        ]);
    }

    /**
     * @Route("/form/{id}", name="form")
     */
    public function form(
        Request $request,
        TranslatableListener $translatableListener,
        ParameterBagInterface $param,
        ValidatorInterface $validator,
        $id = null
    ){
        if ($id) {
            $entity = $this->entityManager->getRepository(Reservation::class)->findOneBy(['id' => $id]);
            if (!$entity instanceof Reservation) {
                throw $this->createNotFoundException('A foglalás nem található.');
            }
        } else {
            $entity = new Reservation();
        }

        $translatableListener->setTranslatableLocale($param->get('locale'));
        $form = $this->createForm(ReservationFormType::class, $entity, ['id' => $id]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $violations = $validator->validate($entity);
            if (count($violations)) {
                throw new ValidationErrorException($violations);
            }
            if ($form->isValid()) {
                if (!$id) {
                    $entity->setLocale('hu');
                    $entity->setReservationStatus(ReservationStatusEnum::MANUAL_RESERVAITON);
                    $entity->setSumPrice(0);
                    $entity->setCreatedAt(new DateTime());
                    $entity->setReservationNumber((new DateTime())->format('ymd') . '/' . substr(md5(rand()), 0, 6));
                }
                $this->entityManager->persist($entity);
                $this->entityManager->flush();
                $this->addFlash('notice', 'Sikeres rögzítés.');
                if (is_null($id) && $entity->getId()) {
                    return $this->redirectToRoute('admin_reservation_form', ['id' => $entity->getId()]);
                }
            } else {
                $this->addFlash('error', 'Sikertelen rögzítés, érvénytelen értékek.');
            }
        }
        return $this->render('admin/reservation/form.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity
        ]);

    }

    /**
     * @Route("/remove/{id}", name="remove")
     */
    public function remove($id)
    {
        $entity = $this->entityManager->getRepository(Reservation::class)->findOneBy(['id' => $id]);
        $op = null;
        if (!$entity instanceof Reservation) {
            throw $this->createNotFoundException('A foglalás nem található.');
        }
        try {
            if ($entity->getReservationStatus() == ReservationStatusEnum::MANUAL_RESERVAITON) {
                $this->entityManager->remove($entity);
                $op = 'manual';
            } else {
                $entity->setReservationStatus(ReservationStatusEnum::DELETED);
                $this->entityManager->persist($entity);
            }
            $this->entityManager->flush();
            $this->addFlash('notice', 'Sikeres törlés.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Sikertelen törlés.' . $e->getMessage());
        }
        return $this->redirectToRoute('admin_reservation_list', ['op' => $op]);
    }

    /**
     * @Route("/remove-item/{id}", name="remove_item")
     */
    public function removeItem($id)
    {
        $entity = $this->entityManager->getRepository(ReservationItem::class)->findOneBy(['id' => $id]);
        if (!$entity instanceof ReservationItem) {
            throw $this->createNotFoundException('A foglalás nem található.');
        }
        try {
            $resrvation = $entity->getReservation();
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
            $this->addFlash('notice', 'Sikeres törlés.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Sikertelen törlés.');
        }
        return $this->redirectToRoute('admin_reservation_form', ['id' => $resrvation->getId()]);
    }

    /**
     * @Route("/calendar", name="calendar")
     */
    public function calendar()
    {
        return $this->render('admin/reservation/calendar.html.twig');
    }

    /**
     * @Route("/vendegek/{id}", name="show_guests")
     */
    public function showguests(Request $request, $id)
    {
        $item = $this->entityManager->getRepository(ReservationItem::class)->findOneBy(['id' => $id]);
        if (!$item instanceof ReservationItem) {
            throw $this->createNotFoundException('A foglalás nem található.');
        }

        $entities = $item->getApartmentGuests()->getValues();
        $return = $item->getReservation()->getId();
        return $this->render('admin/reservation/guests.html.twig', [
            'entities' => $entities,
            'return' => $return
        ]);
    }
}
