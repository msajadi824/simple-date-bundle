<?php

namespace PouyaSoft\SDateBundle\Twig;

use PouyaSoft\SDateBundle\Service\jDateService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PouyaSoftDateExtension extends AbstractExtension
{
    private $jDateService;

    public function __construct(jDateService $jDateService)
    {
        $this->jDateService = $jDateService;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('gpDate', [$this, 'georgianToPersian']),
            new TwigFilter('pgDate', [$this, 'persianToGeorgian']),
            new TwigFilter('glDate', [$this, 'georgianToLocale']),
        ];
    }

    /**
     * @param \DateTime $gDate
     * @param string $format
     * @param string $locale (e.g. fa, fa_IR, en, en_US, en_UK, ...)
     * @param string $calendar (e.g. gregorian, persian, islamic, ...)
     * @param bool $latinizeDigit
     * @return string
     */
    public function georgianToPersian($gDate = null, $format = 'yyyy/MM/dd', $locale = 'fa', $calendar = 'persian', $latinizeDigit = false)
    {
        return $this->jDateService->georgianToPersian($gDate, $format, $locale, $calendar, $latinizeDigit);
    }

    /**
     * @param string $pDate
     * @param string $format
     * @param string $locale (e.g. fa, fa_IR, en, en_US, en_UK, ...)
     * @param string $calendar (e.g. gregorian, persian, islamic, ...)
     * @return \DateTime
     */
    public function persianToGeorgian($pDate, $format = 'yyyy/MM/dd', $locale = 'fa', $calendar = 'persian')
    {
        return $this->jDateService->persianToGeorgian($pDate, $format, $locale, $calendar);
    }

    /**
     * @param \DateTime $gDate
     * @param string $format
     * @param string $locale (e.g. fa, fa_IR, en, en_US, en_UK, ...)
     * @param string $calendar (e.g. gregorian, persian, islamic, ...)
     * @param bool $latinizeDigit
     * @return string
     */
    public function georgianToLocale($gDate = null, $format = 'yyyy/MM/dd', $locale = null, $calendar = null, $latinizeDigit = false)
    {
        return $this->jDateService->georgianToLocale($gDate, $format, $locale, $calendar, $latinizeDigit);
    }

    public function getName()
    {
        return 'pouya_soft.sdate_extension';
    }
}
