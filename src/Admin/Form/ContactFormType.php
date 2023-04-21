<?php

namespace App\Admin\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Név',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ])
            ->add('zip', TextType::class, [
                'label' => 'Irányítószám',
                'required' => true,
            ])
            ->add('city', TextType::class, [
                'label' => 'Város',
                'required' => true,
            ])
            ->add('streetAndOther', TextType::class, [
                'label' => 'Utca, házszám',
                'required' => true,
            ])            
            ->add('phone', TextType::class, [
                'label' => 'Telefonszám',
                'required' => true,
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class
        ]);
    }
}
