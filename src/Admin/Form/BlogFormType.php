<?php

namespace App\Admin\Form;

use App\Entity\Blog;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BlogFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Cím',
                'required' => true,
            ])
            ->add('lead', TextType::class, [
                'label' => 'Lead',
                'required' => true,
            ])
            ->add('body', CKEditorType::class, [
                'label' => 'Szöveg',
                'required' => true,
            ])
            ->add('publishedAt', DateType::class, [
                'label' => 'Publikálva',
                'required' => true,
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Blog::class
        ]);
    }
}
