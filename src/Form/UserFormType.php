<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'forms.profile.email',
                'required' => true,
            ])
            ->add('firstName', TextType::class, [
                'label' => 'forms.profile.firstName',
                'required' => true,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'forms.profile.lastName',
                'required' => true,
            ])
            ->add('phone', TextType::class, [
                'label' => 'forms.profile.phone',
                'required' => true,
            ])
            ->add('invoiceAddressCountry', TextType::class, [
                'label' => 'forms.payment.invoiceAddressCountry',
                'required' => true,
            ])
            ->add('invoiceAddressZip', TextType::class, [
                'label' => 'forms.payment.invoiceAddressZip',
                'required' => true,
            ])
            ->add('invoiceAddressCity', TextType::class, [
                'label' => 'forms.payment.invoiceAddressCity',
                'required' => true,
            ])
            ->add('invoiceAddressStreetAndOther', TextType::class, [
                'label' => 'forms.payment.invoiceAddressStreetAndOther',
                'required' => true,
            ])
            ->add('invoiceAddressName', TextType::class, [
                'label' => 'forms.payment.invoiceAddressName',
                'required' => true,
            ])
            ->add('newsletter', CheckboxType::class, [
                'label_attr' => ['class' => 'switch-custom', "data-toggle" => "tooltip"],
                'label' => 'forms.profile.newsletter',
                'required' => false,
            ])
            ->add('birthDay', DateType::class, [
                'required' => true,
                'widget' => 'single_text',
                'label' => 'forms.profile.birthDay'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => false,
            'locale' => 'hu',
        ]);
    }
}
