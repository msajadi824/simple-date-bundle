<?php
namespace PouyaSoft\SDateBundle\Form\Type;

use PouyaSoft\SDateBundle\Form\DataTransformer\PouyaSoftSDateTransformer;
use PouyaSoft\SDateBundle\Service\jDateService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PouyaSoftSDateType extends AbstractType
{
    private $jDateService;
    protected $locale;

    public function __construct(jDateService $jDateService, RequestStack $requestStack)
    {
        $this->jDateService = $jDateService;
        $this->locale = $requestStack->getCurrentRequest()->getLocale() == 'fa' ? 'fa' : 'en';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new PouyaSoftSDateTransformer($this->jDateService, $options['serverFormat'], $options['locale']);
        $builder->addModelTransformer($transformer);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $defaults = [
            'mddatetimepicker' => true,
            'targetselector' => '#' . $view->vars['id'],
            'placement' => 'bottom',
            'trigger' => 'focus',
            'enableTimePicker' => false,
            'groupId' => '',
            'fromDate' => false,
            'toDate' => false,
            'disableBeforeToday' => false,
            'disabled' => false,
            'textFormat' => 'yyyy/MM/dd',
            'isGregorian' => $options['locale'] !== 'fa',
            'englishNumber' => $options['locale'] !== 'fa',
        ];

        $pickerOptions = array_merge($defaults, $options['pickerOptions'] ?? []);

        $view->vars['attr'] = array_merge($view->vars['attr'] ?? [], $this->convertOptionsToDataAttributes($pickerOptions));
        $view->vars['locale'] = $options['locale'];
    }

    private function convertOptionsToDataAttributes(array $options): array
    {
        $attributes = ['data-mddatetimepicker' => 'true'];

        foreach ($options as $key => $value) 
            $attributes['data-' . $key] = is_bool($value) ? ($value ? 'true' : 'false') : $value;
        
        return $attributes;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message' => $this->locale == 'fa' ? 'تاریخ وارد شده اشتباه است' : 'Selected date is invalid.',
            'serverFormat' => 'yyyy/MM/dd',
            'locale' => $this->locale,
            'pickerOptions' => [],
        ]);

        $resolver->setAllowedTypes('serverFormat', ['string', 'null']);
        $resolver->setAllowedTypes('locale', ['string', 'null']);
        $resolver->setAllowedValues('locale', ['fa', 'en', null]);
        $resolver->setAllowedTypes('pickerOptions', ['array', 'null']);
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function getBlockPrefix()
    {
        return 'jSDate';
    }
} 