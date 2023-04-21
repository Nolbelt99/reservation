<?php

namespace App\Admin\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;

class ReservationFilter extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userEmail', Filters\TextFilterType::class, [
                'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                    if (empty($values['value'])) {
                        return null;
                    } else {
                        $expression = $filterQuery->getExpr()->orX(
                            $filterQuery->getExpr()->like('user.email', $filterQuery->getExpr()->literal('%' . trim($values['value']) . '%')),
                        );
                        return $filterQuery->createCondition($expression);
                    }
                },
                'attr' => [
                    'placeholder' => 'Felhasználó email cím',
                ]
            ])
            ->setMethod('GET');
    }

    public function getBlockPrefix()
    {
        return 'filter';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering')
        ));
    }
}
