<?php
namespace PouyaSoft\SDateBundle\Form\Type;

use farhadi\IntlDateTime;
use PouyaSoft\SDateBundle\Form\DataTransformer\PouyaSoftSDateTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PouyaSoftSDateType extends AbstractType
{
    /**
     * @var IntlDateTime
     */
    private $jDateService;

    /**
     * @param IntlDateTime $jDateService
     */
    public function __construct(IntlDateTime $jDateService)
    {
        $this->jDateService = $jDateService;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new PouyaSoftSDateTransformer($this->jDateService, $options['format']);
        $builder->addModelTransformer($transformer);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'تاریخ وارد شده اشتباه است',
            'format' => 'Y/m/d'
        ));

        $resolver->setAllowedTypes('format', ['string', 'null']);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'تاریخ وارد شده اشتباه است',
            'format' => 'Y/m/d'
        ));

        $resolver->setAllowedTypes(array(
            'format' => array('string', 'null')
        ));
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'jSDate';
    }
} 