<?php

namespace PouyaSoft\SDateBundle\Twig;

use PouyaSoft\SDateBundle\Service\jDateService;

class PouyaSoftDateExtension extends \Twig_Extension
{
    /** @var  jDateService */
    private $jDateService;

    public function __construct($jDateService)
    {
        $this->jDateService = $jDateService;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('gpDate', array($this, 'georgianToPersian')),
            new \Twig_SimpleFilter('pgDate', array($this, 'persianToGeorgian'))
        );
    }

    /**
     * @param \DateTime $gDate
     * @param string $format
     * @return string
     */
    public function georgianToPersian($gDate, $format = 'yyyy/MM/dd')
    {
        return $this->jDateService->georgianToPersian($gDate, $format);
    }

    /**
     * @param string $pDate
     * @param string $format
     * @return \DateTime
     */
    public function persianToGeorgian($pDate, $format = 'yyyy/MM/dd')
    {
        return $this->jDateService->persianToGeorgian($pDate, $format);
    }

    public function getName()
    {
        return 'pouya_soft.sdate_extension';
    }
}