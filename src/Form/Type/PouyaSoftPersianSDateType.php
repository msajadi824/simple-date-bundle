<?php
namespace PouyaSoft\SDateBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PouyaSoftPersianSDateType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $options['pickerOptions']['targetselector'] = '#' . $view->vars['id'];

        $view->vars['attr'] = array_merge(
            $view->vars['attr'] ?? [],
            $this->convertOptionsToDataAttributes($options['pickerOptions'])
        );
    }

    protected function convertOptionsToDataAttributes(array $options): array
    {
        $attributes = ['data-mddatetimepicker' => 'true'];

        foreach ($options as $key => $value) {
            $attributes['data-' . $key] = is_bool($value) ? ($value ? 'true' : 'false') : (string) $value;
        }

        return $attributes;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $defaultPickerOptions = [
            'mddatetimepicker' => true,
            'targetselector' => '', // در buildView مقداردهی می‌شود
            'placement' => 'bottom',
            'trigger' => 'focus',
            'enableTimePicker' => false,
            'groupId' => '',
            'fromDate' => false,
            'toDate' => false,
            'disableBeforeToday' => false,
            'disabled' => false,
            'textFormat' => 'yyyy/MM/dd',
            'isGregorian' => false,
            'englishNumber' => false,
        ];

        $resolver->setDefaults([
            'pickerOptions' => $defaultPickerOptions,
        ]);

        $resolver->setAllowedTypes('pickerOptions', ['array', 'null']);
    }

    public function getParent(): string
    {
        return TextType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'persianSDate';
    }
}