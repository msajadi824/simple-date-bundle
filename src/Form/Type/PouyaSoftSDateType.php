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
    private jDateService $jDateService;
    private string $locale;

    public function __construct(jDateService $jDateService, RequestStack $requestStack)
    {
        $request = $requestStack->getCurrentRequest();
        $this->locale = $request && $request->getLocale() === 'fa' ? 'fa' : 'en';
        $this->jDateService = $jDateService;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array<string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $transformer = new PouyaSoftSDateTransformer(
            $this->jDateService,
            $options['serverFormat'],
            $options['locale']
        );
        $builder->addModelTransformer($transformer);
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array<string, mixed> $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        // Always override targetselector with the current field ID
        $options['pickerOptions']['targetselector'] = '#' . $view->vars['id'];

        $view->vars['attr'] = array_merge(
            $view->vars['attr'] ?? [],
            $this->convertOptionsToDataAttributes($options['pickerOptions'])
        );

        $view->vars['locale'] = $options['locale'];
    }

    /**
     * Converts an associative array to HTML data-* attributes
     *
     * @param array<string, mixed> $options
     * @return array<string, string>
     */
    private function convertOptionsToDataAttributes(array $options): array
    {
        $attributes = ['data-mddatetimepicker' => 'true'];

        foreach ($options as $key => $value) {
            $attributes['data-' . $key] = is_bool($value) ? ($value ? 'true' : 'false') : (string) $value;
        }

        return $attributes;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $defaultPickerOptions = [
            'mddatetimepicker' => true,
            'targetselector' => '', // will be overridden in buildView
            'placement' => 'bottom',
            'trigger' => 'focus',
            'enableTimePicker' => false,
            'groupId' => '',
            'fromDate' => false,
            'toDate' => false,
            'disableBeforeToday' => false,
            'disabled' => false,
            'textFormat' => 'yyyy/MM/dd',
            'isGregorian' => $this->locale !== 'fa',
            'englishNumber' => $this->locale !== 'fa',
        ];

        $resolver->setDefaults([
            'invalid_message' => $this->locale === 'fa' ? 'تاریخ وارد شده اشتباه است' : 'Selected date is invalid.',
            'serverFormat' => 'yyyy/MM/dd',
            'locale' => $this->locale,
            'pickerOptions' => $defaultPickerOptions,
        ]);

        $resolver->setAllowedTypes('serverFormat', ['string', 'null']);
        $resolver->setAllowedTypes('locale', ['string', 'null']);
        $resolver->setAllowedValues('locale', ['fa', 'en', null]);
        $resolver->setAllowedTypes('pickerOptions', ['array', 'null']);
    }

    public function getParent(): string
    {
        return TextType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'jSDate';
    }
}
