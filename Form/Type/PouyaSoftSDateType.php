<?php
namespace PouyaSoft\SDateBundle\Form\Type;

use PouyaSoft\SDateBundle\Form\DataTransformer\PouyaSoftSDateTransformer;
use PouyaSoft\SDateBundle\Service\jDateService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PouyaSoftSDateType extends AbstractType
{
    /**
     * @var jDateService
     */
    private $jDateService;

    /**
     * @param jDateService $jDateService
     */
    public function __construct(jDateService $jDateService)
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
            'format' => 'yyyy/MM/dd'
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
            'format' => 'yyyy/MM/dd'
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