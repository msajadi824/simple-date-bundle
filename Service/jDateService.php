<?php

namespace PouyaSoft\SDateBundle\Service;

use PouyaSoft\SDateBundle\Lib\IntlDateTime;

class jDateService
{
    /**
     * @param string $persian
     * @param string $format
     * @return \DateTime
     */
    public function persianToGeorgian($persian, $format = 'yyyy/MM/dd')
    {
        return $this->intlDateTimeInstance($persian, null, 'persian', 'fa', $format);
    }

    /**
     * @param \DateTime $georgian
     * @param string $format
     * @return string
     */
    public function georgianToPersian(\DateTime $georgian, $format = 'yyyy/MM/dd')
    {
        return $this->intlDateTimeInstance($georgian, null, 'persian', 'fa', null)->intlFormat($format);
    }

    /**
     * Creates a new instance of IntlDateTime
     *
     * @param mixed $time Unix timestamp or strtotime() compatible string or another DateTime object
     * @param mixed $timezone DateTimeZone object or timezone identifier as full name (e.g. Asia/Tehran) or abbreviation (e.g. IRDT).
     * @param string $calendar any calendar supported by ICU (e.g. gregorian, persian, islamic, ...)
     * @param string $locale any locale supported by ICU
     * @param string $pattern the date pattern in which $time is formatted.
     * @return IntlDateTime
     */
    public function intlDateTimeInstance($time = null, $timezone = null, $calendar = 'gregorian', $locale = 'en_US', $pattern = null)
    {
        return new IntlDateTime($time, $timezone, $calendar, $locale, $pattern);
    }
}