<?php

namespace App\Admin\Form;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use App\Admin\Form\Event\Subscriber\Translatable;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TranslatableType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var ParameterBagInterface
     */
    protected $param;

    /**
     * @var array
     */
    protected $locales;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @param EntityManagerInterface $em
     * @param ValidatorInterface $validator
     * @param ParameterBagInterface $param
     * @param array $locales
     * @param string $locale
     */
    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator, ParameterBagInterface $param, array $locales, $locale)
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->locales = $locales;
        $this->locale = $locale;
        $this->param = $param;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (! class_exists($options['personal_translation'])) {
            throw $this->getNoPersonalTranslationException($options['personal_translation']);
        }

        $options['field'] = $options['field'] ?: $builder->getName();

        $builder->addEventSubscriber(
            new Translatable($builder->getFormFactory(), $this->em, $this->validator, $this->param, $options)
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults($this->getDefaultOptions());
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(array $options = array())
    {
        $options['remove_empty'] = true; // Personal Translations without content are removed
        $options['csrf_protection'] = false;
        $options['personal_translation'] = false; // Personal Translation class
        $options['locales'] = $this->locales; // the locales you wish to edit
        $options['required_locale'] = [$this->locale]; // the required locales cannot be blank
        $options['field'] = false; // the field that you wish to translate
        $options['widget'] = TextType::class; // change this to another widget like 'texarea' if needed
        $options['entity_manager_removal'] = true; // auto removes the Personal Translation thru entity manager
        $options['attr'] = [];
        $options['empty_data'] = function (FormInterface $form) {
            return new \Doctrine\Common\Collections\ArrayCollection();
        };
        return $options;
    }

    /**
     * @param string $translation
     *
     * @return \InvalidArgumentException
     */
    public function getNoPersonalTranslationException($translation)
    {
        return new \InvalidArgumentException(sprintf('Unable to find personal translation class: "%s"', $translation));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'translatable';
    }
}
