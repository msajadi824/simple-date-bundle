<?php
namespace PouyaSoft\SDateBundle\Form\Type;

use PouyaSoft\SDateBundle\Form\DataTransformer\PouyaSoftSDateTransformer;
use PouyaSoft\SDateBundle\Service\jDateService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PouyaSoftSDateType extends AbstractType
{
    /** @var jDateService */
    private $jDateService;

    /** @var string */
    protected $locale;

    public function __construct(jDateService $jDateService, RequestStack $requestStack)
    {
        $this->jDateService = $jDateService;
        $this->locale = $requestStack->getCurrentRequest()->getLocale() == 'fa' ? 'fa' : 'en';
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new PouyaSoftSDateTransformer($this->jDateService, $options['serverFormat'], $options['locale']);
        $builder->addModelTransformer($transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['pickerOptions'] = $options['pickerOptions'];
        $view->vars['locale'] = $options['locale'];
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => $this->locale == 'fa' ? 'تاریخ وارد شده اشتباه است' : 'Selected date is invalid.',
            'serverFormat' => 'yyyy/MM/dd',
            'locale' => $this->locale,
            'pickerOptions' => [],
        ));

        $resolver->setAllowedTypes('serverFormat', ['string', 'null']);
        $resolver->setAllowedTypes('locale', ['string', 'null']);
        $resolver->setAllowedValues('locale', ['fa', 'en', null]);
        $resolver->setAllowedTypes('pickerOptions', ['array', 'null']);
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
        return 'Symfony\Component\Form\Extension\Core\Type\TextType';
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'jSDate';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
} 