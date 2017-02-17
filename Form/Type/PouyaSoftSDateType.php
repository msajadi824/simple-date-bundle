<?php
namespace PouyaSoft\SDateBundle\Form\Type;

use PouyaSoft\SDateBundle\Form\DataTransformer\PouyaSoftSDateTransformer;
use PouyaSoft\SDateBundle\Service\jDateService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
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
        $transformer = new PouyaSoftSDateTransformer($this->jDateService, $options['serverFormat']);
        $builder->addModelTransformer($transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['clientFormat'] = $options['clientFormat'];
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'تاریخ وارد شده اشتباه است',
            'serverFormat' => 'yyyy/MM/dd',
            'clientFormat' => 'yy/m/d',
        ));

        $resolver->setAllowedTypes('serverFormat', ['string', 'null']);
        $resolver->setAllowedTypes('clientFormat', ['string', 'null']);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
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