<?php

namespace App\Admin\Form;

use App\Entity\Page;
use App\Entity\PageTranslation;
use App\Admin\Form\TranslatableType;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TranslatableType::class, array(
                'label' => 'Cím',
                'field' => 'title',
                'widget' => TextType::class,
                'property_path' => 'translations',
                'personal_translation' => PageTranslation::class,
                'attr' => ['required' => true]
            ))
            ->add('slug', TranslatableType::class, array(
                'label' => 'Slug',
                'field' => 'slug',
                'widget' => TextType::class,
                'attr' => ['required' => true],
                'property_path' => 'translations',
                'personal_translation' => PageTranslation::class
            ))
            ->add('body', TranslatableType::class, array(
                'label' => 'Szöveg',
                'field' => 'body',
                'widget' => CKEditorType::class,
                'property_path' => 'translations',
                'personal_translation' => PageTranslation::class,
                'attr' => ['required' => true]
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Page::class
        ]);
    }
}
