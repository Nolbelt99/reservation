<?php

namespace App\Admin\Form;

use App\Entity\Service;
use App\Enum\CaptainTypeEnum;
use App\Enum\ReservationTypeEnum;
use App\Entity\ServiceTranslation;
use Doctrine\ORM\EntityRepository;
use App\Admin\Form\TranslatableType;
use App\Enum\ServiceTypeEnum;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ServiceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TranslatableType::class, array(
                'label' => 'Név',
                'field' => 'name',
                'widget' => TextType::class,
                'property_path' => 'translations',
                'personal_translation' => ServiceTranslation::class,
                'attr' => ['required' => true]
            ))
            ->add('slug', TranslatableType::class, array(
                'label' => 'Slug',
                'field' => 'slug',
                'widget' => TextType::class,
                'attr' => ['required' => true],
                'property_path' => 'translations',
                'personal_translation' => ServiceTranslation::class
            ))
            ->add('lead', TranslatableType::class, array(
                'label' => 'Rövid szöveges leírás',
                'field' => 'lead',
                'widget' => TextType::class,
                'property_path' => 'translations',
                'personal_translation' => ServiceTranslation::class,
                'attr' => ['required' => true]
            ))
            ->add('body', TranslatableType::class, array(
                'label' => 'Hosszú leírás',
                'field' => 'body',
                'widget' => CKEditorType::class,
                'property_path' => 'translations',
                'personal_translation' => ServiceTranslation::class,
                'attr' => ['required' => true]
            ))
            ->add('coverImage', FileType::class, [
                'label' => 'Nyitó kép',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '24M',
                        'mimeTypes' => [
                            'image/png',
                            'image/bmp',
                            'image/gif',
                            'image/jpeg',
                            'image/svg+xml',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Csak képet lehet feltölteni',
                    ])
                ]
            ])
            ->add('coverImageCollection', FileType::class, [
                'label' => 'Lista kép',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '24M',
                        'mimeTypes' => [
                            'image/png',
                            'image/bmp',
                            'image/gif',
                            'image/jpeg',
                            'image/svg+xml',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Csak képet lehet feltölteni',
                    ])
                ]
            ])
            ->add('reservationType', ChoiceType::class, [
                'label' => 'Foglalás típusa',
                'placeholder' => 'Válasszon!',
                'required' => true,
                'choices' => ReservationTypeEnum::getChoices(),
                'choice_label' => function($choice) {
                    return ReservationTypeEnum::getName($choice);
                },
            ])
            ->add('avaibleSameTime', NumberType::class, [
                'label' => 'Szolgáltatás elérhető mennyisége egyidőben',
                'required' => true
            ])
            ->add('minDay', NumberType::class, [
                'label' => 'Minimum foglalható napok száma',
                'required' => true,
                'attr' => [
                    'min' => 1
                ]
            ])
            ->add('minGiftDay', NumberType::class, [
                'label' => 'Kapcsolódó ajándék minimum foglalás nap',
                'required' => false
            ])
            ->add('giftText', TranslatableType::class, array(
                'label' => 'Kapcsolódó ajándék leírás',
                'field' => 'giftText',
                'required' => false,
                'widget' => TextType::class,
                'property_path' => 'translations',
                'personal_translation' => ServiceTranslation::class,
                'attr' => ['required' => false]
            ))
            ->add('giftImage', FileType::class, [
                'label' => 'Kapcsolódó ajándék szolgáltatás ajánlókép',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '24M',
                        'mimeTypes' => [
                            'image/png',
                            'image/bmp',
                            'image/gif',
                            'image/jpeg',
                            'image/svg+xml',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Csak képet lehet feltölteni',
                    ])
                ]
            ])
            ->add('galleryImages', FileType::class, [
                'label' => 'Galléria képek',
                'required' => false,
                'mapped' => false,
                'multiple' => true,
                'data_class' => null,
                'constraints' => [
                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '24M',
                                'mimeTypes' => [
                                    'image/png',
                                    'image/bmp',
                                    'image/gif',
                                    'image/jpeg',
                                    'image/svg+xml',
                                    'image/webp',
                                ],
                                'mimeTypesMessage' => 'Csak képet lehet feltölteni',
                            ])
                        ]
                    ])
                ]
            ])
            ->add('giftService', EntityType::class, [
                'label' => 'Kapcsolódó ajándék szolgáltatás',
                'class' => Service::class,
                'required' => false,
                'choice_label' => 'name',
                'placeholder' => 'Válassz!',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('t')
                        ->andWhere('t.deleted = false')
                        ->andWhere('t.id != :id')
                        ->setParameter('id', $options['id']);
                }
            ])
            ->add('relatedServices', EntityType::class, [
                'label' => 'Kapcsolódó szolgáltatások',
                'class' => Service::class,
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'choice_label' => 'name',
                'placeholder' => 'Válassz!',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('t')
                        ->andWhere('t.deleted = false')
                        ->andWhere('t.id != :id')
                        ->setParameter('id', $options['id']);
                }
            ])
            ->add('conditionServices', EntityType::class, [
                'label' => 'Feltétel szolgáltatások',
                'class' => Service::class,
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'choice_label' => 'name',
                'placeholder' => 'Válasszon!',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('t')
                        ->andWhere('t.deleted = false')
                        ->andWhere('t.id != :id')
                        ->andWhere('t.reservationType = :independent')
                        ->setParameter('id', $options['id'])
                        ->setParameter('independent', ReservationTypeEnum::INDEPENDENT);
                }
            ])
            ->add('companyName', ChoiceType::class, [
                'choices' => $options['companies'],
                'label' => 'Számla kibocsátó vállalkozás',
                'required' => true,
                'choice_label' => function($choice) {
                    return $choice;
                },
            ])
        ;


        if ($options['op'] == 'apartment') {
            $builder
            ->add('beds', NumberType::class, [
                'label' => 'Férőhelyek száma',
                'required' => true
            ])
            ->add('extraBeds', NumberType::class, [
                'label' => 'Férőhelyek száma',
                'required' => true
            ])
            ->add('serviceType', HiddenType::class, [
                'data' => ServiceTypeEnum::APARTMENT
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Ár',
                'currency' => 'HUF',
                'grouping' => true,
                'required' => true
            ]);
        }

        if ($options['op'] == 'ebike') {
            $builder
            ->add('halfDayPrice', MoneyType::class, [
                'label' => 'Fél nap ár',
                'currency' => 'HUF',
                'grouping' => true,
                'required' => true
            ])
            ->add('serviceType', HiddenType::class, [
                'data' => ServiceTypeEnum::EBIKE
            ])
            ->add('fullDayPrice', MoneyType::class, [
                'label' => 'Egész nap ár',
                'currency' => 'HUF',
                'grouping' => true,
                'required' => true
            ]);
        }

        if ($options['op'] == 'ship') {
            $builder
            ->add('assurance', MoneyType::class, [
                'label' => 'Biztosíték összege',
                'currency' => 'HUF',
                'grouping' => true,
                'required' => true
            ])
            ->add('cleaningCharge', MoneyType::class, [
                'label' => 'Takarítási díj',
                'currency' => 'HUF',
                'grouping' => true,
                'required' => true
            ])
            ->add('captainType', ChoiceType::class, [
                'label' => 'Kapitány',
                'placeholder' => 'Válasszon!',
                'required' => true,
                'choices' => CaptainTypeEnum::getChoices(),
                'choice_label' => function($choice) {
                    return CaptainTypeEnum::getName($choice);
                }
            ])
            ->add('serviceType', HiddenType::class, [
                'data' => ServiceTypeEnum::SHIP
            ])
            ->add('captainPrice', MoneyType::class, [
                'label' => 'Kapitány díj',
                'currency' => 'HUF',
                'grouping' => true,
                'required' => false
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
            'op' => '',
            'companies' => [],
            'id' => 0
        ]);
    }
}
