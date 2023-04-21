<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('invoiceAddressName', TextType::class, [
                'required' => true,
                'label' => 'forms.payment.invoiceAddressName',
            ])
            ->add('invoiceAddressZip', TextType::class, [
                'required' => true,
                'label' => 'forms.payment.invoiceAddressZip',
            ])
            ->add('invoiceAddressCountry', ChoiceType::class, [
                'choices' => $options['countries'],
                'label' => 'forms.payment.invoiceAddressCountry',
                'required' => true,
            ])
            ->add('invoiceAddressCity', TextType::class, [
                'required' => true,
                'label' => 'forms.payment.invoiceAddressCity',
            ])
            ->add('invoiceAddressStreetAndOther', TextType::class, [
                'required' => true,
                'label' => 'forms.payment.invoiceAddressStreetAndOther',
            ])
            ->add('data', CheckboxType::class, [
                'mapped' => false,
                'constraints' =>  new IsTrue(["message" => "A hozzájárulás elfogadása kötelező"]),
                'label' =>  'forms.payment.data',
                'label_html' => true
            ])
            ->add('valid_data', CheckboxType::class, [
                'mapped' => false,
                'constraints' =>  new IsTrue(["message" => "A hozzájárulás elfogadása kötelező"]),
                'label' => 'forms.payment.valid_data',

            ])
            ->add('terms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => new IsTrue(["message" => "A feltételek elfogadása kötelező"]),
                'label' => 'forms.payment.terms',
                'label_html' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => false,
            'countries' => [],
        ]);
    }
}
