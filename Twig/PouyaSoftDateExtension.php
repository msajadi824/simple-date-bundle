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
            new \Twig_SimpleFilter('jSDate', array($this, 'jSDateFilter')),
            new \Twig_SimpleFilter('jSDayWeek', array($this, 'jSDayWeekFilter'))
        );
    }

    /**
     * @param \DateTime $date
     * @param string $separator
     * @param bool $hasTime
     * @return string
     */
    public function jSDateFilter($date, $separator = null, $hasTime = false)
    {
        return $this->jDateService->MiladiToShamsi($date, $separator, $hasTime);
    }

    /**
     * @param \DateTime $date
     * @return string
     */
    public function jSDayWeekFilter($date)
    {
        return $this->jDateService->getWeekDay($date);
    }

    public function getName()
    {
        return 'pouya_soft.sdate_extension';
    }
}