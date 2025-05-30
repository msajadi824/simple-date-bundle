<?php
namespace PouyaSoft\SDateBundle\Form\Type;

use PouyaSoft\SDateBundle\Form\DataTransformer\PouyaSoftSDateTransformer;
use PouyaSoft\SDateBundle\Service\jDateService;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PouyaSoftSDateType extends PouyaSoftPersianSDateType
{
    private jDateService $jDateService;
    private string $locale;

    public function __construct(jDateService $jDateService, RequestStack $requestStack)
    {
        $this->jDateService = $jDateService;
        $this->locale = $requestStack->getCurrentRequest()->getLocale() === 'fa' ? 'fa' : 'en';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $transformer = new PouyaSoftSDateTransformer(
            $this->jDateService,
            $options['serverFormat'],
            $options['locale']
        );

        $builder->addModelTransformer($transformer);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['attr']['data-isGregorian'] = $options['locale'] !== 'fa' ? 'true' : 'false';
        $view->vars['attr']['data-englishNumber'] = $options['locale'] !== 'fa' ? 'true' : 'false';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'serverFormat' => 'yyyy/MM/dd',
            'locale' => $this->locale,
            'invalid_message' => $this->locale === 'fa'
                ? 'تاریخ وارد شده اشتباه است'
                : 'Selected date is invalid.',
        ]);

        $resolver->setAllowedTypes('serverFormat', ['string', 'null']);
        $resolver->setAllowedTypes('locale', ['string', 'null']);
        $resolver->setAllowedValues('locale', ['fa', 'en', null]);
    }

    public function getBlockPrefix(): string
    {
        return 'jSDate';
    }
}
