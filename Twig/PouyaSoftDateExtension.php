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
            new \Twig_SimpleFilter('jSDate', array($this, 'jSDateFilter'))
        );
    }

    /**
     * @param \DateTime $date
     * @param string $separator
     * @return string
     */
    public function jSDateFilter($date, $separator = null)
    {
        return $this->jDateService->MiladiToShamsi($date, $separator);
    }

    public function getName()
    {
        return 'pouya_soft.sdate_extension';
    }
}