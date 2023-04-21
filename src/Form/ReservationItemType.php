<?php

namespace App\Form;

use App\Entity\Service;
use App\Enum\CaptainTypeEnum;
use App\Enum\ServiceTypeEnum;
use App\Entity\ReservationItem;
use App\Validator\Constraints\ValidDate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ReservationItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Service $service */
        $service = $options['service'];
        $builder
            ->add('startDate', TextType::class, [
                'label' => 'forms.reservationItem.startDate',
                'required' => true,
                'mapped' => false,
                'constraints' => [new ValidDate()]
            ])
            ->add('endDate', TextType::class, [
                'label' => 'forms.reservationItem.endDate',
                'required' => true,
                'mapped' => false,
                'constraints' => [new ValidDate()]
            ])
        ;

        if ($service->getCaptainType() != CaptainTypeEnum::MANDATORY && $service->getServiceType() == ServiceTypeEnum::SHIP) {
            $builder
                ->add('withCaptain', ChoiceType::class, [
                    'label' => 'forms.reservationItem.withCaptain',
                    'choices' => [
                        'Igen' => true,
                        'Nem' => false,
                    ],
                    'required' => true,
                ]);
        }
        if ($service->getCaptainType() == CaptainTypeEnum::OPTIONAL_WITH_LICENCE) {
            $builder
                ->add('licenceNumber', TextType::class, [
                    'label' => 'forms.reservationItem.licenceNumber',
                    'required' => false,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReservationItem::class,
            'csrf_protection' => false,
            'service' => null
        ]);
    }
}
