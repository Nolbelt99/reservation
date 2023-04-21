<?php

namespace App\Admin\Form;

use App\Entity\Service;
use App\Entity\ReservationItem;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ReservationItemsCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('service', EntityType::class, [
                'label' => 'Szolgáltatás',
                'class' => Service::class,
                'required' => false,
                'choice_label' => 'name',
                'placeholder' => 'Válassz!',
                'query_builder' => function (EntityRepository $er){
                    $queryBuilder = $er->createQueryBuilder('t');
                    return $queryBuilder
                        ->andWhere('t.deleted = false');
                }
            ])
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => 'Dátumtól'
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => 'Dátumig'
            ])
            ->add('reservationPaidSuccesfully', CheckboxType::class, [
                'label_attr' => ['class' => 'switch-custom', "data-toggle" => "tooltip"],
                'label' => 'Foglalás sikeresen fizetve',
                'disabled' => true
            ])
            ->add('reservationPrice', MoneyType::class, [
                'label' => 'Fizetett foglalási díj',
                'currency' => 'HUF',
                'grouping' => true,
                'disabled' => true
            ])
            ->add('paidAssurance', MoneyType::class, [
                'label' => 'Fizetett biztosíték összege',
                'currency' => 'HUF',
                'grouping' => true,
                'disabled' => true
            ])
            ->add('assurancePaidSuccesfully', CheckboxType::class, [
                'label_attr' => ['class' => 'switch-custom', "data-toggle" => "tooltip"],
                'label' => 'Biztosíték sikeresen fizetve',
                'disabled' => true
            ])
            ->add('withCaptain', CheckboxType::class, [
                'label_attr' => ['class' => 'switch-custom', "data-toggle" => "tooltip"],
                'label' => 'Kapitánnyal',
                'disabled' => true
            ])
            ->add('licenceNumber', TextType::class, [
                'label' => 'Engedély szám',
                'disabled' => true
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ReservationItem::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'ReservationItemType';
    }
}
