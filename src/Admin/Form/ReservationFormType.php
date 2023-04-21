<?php

namespace App\Admin\Form;

use App\Entity\User;
use App\Entity\Reservation;
use Doctrine\ORM\EntityRepository;
use App\Enum\ReservationStatusEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ReservationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['id'] == null) {
            $builder
                ->add('user', EntityType::class, [
                    'label' => 'Felhasználó',
                    'class' => User::class,
                    'required' => true,
                    'choice_label' => 'email',
                    'placeholder' => 'Válassz!',
                    'query_builder' => function (EntityRepository $er){
                        $queryBuilder = $er->createQueryBuilder('t');
                        return $queryBuilder
                            ->andWhere($queryBuilder->expr()->like('t.roles', ':role'))
                            ->setParameter('role', '%ROLE_ADMIN%');
                    }
                ])
                ->add('reservationItems', CollectionType::class, [
                    'entry_type' => ReservationItemsCollectionType::class,
                    'allow_delete' => true,
                    'allow_add' => true,
                    'prototype' => true,
                    'attr' => array(
                        'class' => 'ReservationItemsCollection'
                    ),
                    'by_reference' => false,
                    'constraints' => new Valid(),
                    'label' => 'Foglalások'
                ])
                ;
        } else {
            $builder
            ->add('reservationItems', CollectionType::class, [
                'entry_type' => ReservationItemsCollectionType::class,
                'allow_delete' => false,
                'allow_add' => false,
                'prototype' => true,
                'attr' => array(
                    'class' => 'ReservationItemsCollection'
                ),
                'by_reference' => false,
                'constraints' => new Valid(),
                'label' => 'Foglalások'
            ])
            ->add('reservationStatus', ChoiceType::class, [
                'label' => 'Foglalás státusza',
                'placeholder' => 'Válasszon!',
                'required' => true,
                'choices' => ReservationStatusEnum::getChoicesAdmin(),
                'choice_label' => function($choice) {
                    return ReservationStatusEnum::getName($choice);
                }
            ])
            ->add('locale', TextType::class, [
                'disabled' => true,
                'label' => 'Foglalás nyelve'
            ])
            ->add('reservationNumber', TextType::class, [
                'disabled' => true,
                'label' => 'Foglalás azonosító'
            ])
            ->add('createdAt', DateType::class, [
                'disabled' => true,
                'widget' => 'single_text',
                'label' => 'Foglalás ideje'
            ])
            ->add('sumPrice', MoneyType::class, [
                'label' => 'Foglalás értéke',
                'currency' => 'HUF',
                'grouping' => true,
                'disabled' => true
            ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'id' => null,
            'reservationPaidSuccesfully' => false,
            'assurancePaidSuccesfully' => false
        ]);
    }
}
