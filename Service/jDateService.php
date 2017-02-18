<?php

namespace PouyaSoft\SDateBundle\Service;

use PouyaSoft\SDateBundle\Lib\IntlDateTime;

class jDateService
{
    /**
     * @param string $persian
     * @param string $format
     * @param string $locale (e.g. fa, fa_IR, en, en_US, en_UK, ...)
     * @param string $calendar (e.g. gregorian, persian, islamic, ...)
     * @return \DateTime
     */
    public function persianToGeorgian($persian, $format = 'yyyy/MM/dd', $locale = 'fa', $calendar = 'persian')
    {
        return $this->intlDateTimeInstance($persian, null, $calendar, $locale, $format);
    }

    /**
     * @param \DateTime $georgian
     * @param string $format
     * @param string $locale (e.g. fa, fa_IR, en, en_US, en_UK, ...)
     * @param string $calendar (e.g. gregorian, persian, islamic, ...)
     * @param bool $latinizeDigit
     * @return string
     */
    public function georgianToPersian(\DateTime $georgian = null, $format = 'yyyy/MM/dd', $locale = 'fa', $calendar = 'persian', $latinizeDigit = true)
    {
        return $this->intlDateTimeInstance($georgian, null, $calendar, $locale, null)->intlFormat($format, null, $latinizeDigit);
    }

    /**
     * Creates a new instance of IntlDateTime
     *
     * @param mixed $time Unix timestamp or strtotime() compatible string or another DateTime object
     * @param mixed $timezone DateTimeZone object or timezone identifier as full name (e.g. Asia/Tehran) or abbreviation (e.g. IRDT).
     * @param string $calendar any calendar supported by ICU (e.g. gregorian, persian, islamic, ...)
     * @param string $locale any locale supported by ICU (e.g. fa, fa_IR, en, en_US, en_UK, ...)
     * @param string $pattern the date pattern in which $time is formatted.
     * @return IntlDateTime
     */
    public function intlDateTimeInstance($time = null, $timezone = null, $calendar = 'gregorian', $locale = 'en_US', $pattern = null)
    {
        return new IntlDateTime($time, $timezone, $calendar, $locale, $pattern);
    }
}