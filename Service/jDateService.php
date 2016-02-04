<?php

namespace PouyaSoft\SDateBundle\Service;

use PouyaSoft\SDateBundle\Lib\JalaliDateConverter;

class jDateService
{
    /**
     * @param string $shamsi
     * @param string $separator
     * @return \DateTime|null
     * @throws \Exception
     */
    public function ShamsiToMiladi($shamsi = '', $separator = '/')
    {
        if ($shamsi == null || $shamsi == '')
            return null;

        try {
            $shamsiarray = explode($separator, $shamsi);

            if(strlen($shamsiarray[0]) != 4 || strlen($shamsiarray[1]) < 1 || strlen($shamsiarray[1]) > 2 || strlen($shamsiarray[2]) < 1 || strlen($shamsiarray[2]) > 2) throw new \Exception();

            if ($shamsiarray[1] > 12 || $shamsiarray[2] > 31) throw new \Exception();
            if ($shamsiarray[1] > 6 && $shamsiarray[2] > 30) throw new \Exception();
            if ($shamsiarray[1] == 12 && $shamsiarray[2] > 29 && !$this->isLeapYear($shamsiarray[0])) throw new \Exception();

            $miladiarray = JalaliDateConverter::toGregorian($shamsiarray[0], $shamsiarray[1], $shamsiarray[2]);

            return date_create_from_format('Y/m/d', $miladiarray[0] . '/' . $miladiarray[1] . '/' . $miladiarray[2]);

        } catch (\Exception $e) {
            throw new \Exception('Invalid date.');
        }
    }

    /**
     * @param \DateTime $miladi
     * @param string $separator
     * @param bool $hasTime
     * @return string
     */
    public function MiladiToShamsi(\DateTime $miladi = null, $separator = '/', $hasTime = false)
    {
        if ($miladi == null)
            return '';

        $separator = $separator ?: '/';

        $miladiarray = date_parse(date_format($miladi, 'Y/m/d H:i'));
        $shamsiarray = JalaliDateConverter::toJalali($miladiarray['year'], $miladiarray['month'], $miladiarray['day']);
        return $shamsiarray[0] . $separator . $shamsiarray[1] . $separator . $shamsiarray[2]
        .( $hasTime ? '  ' . $this->addZeroBefore($miladiarray['hour']) . ':' .$this->addZeroBefore($miladiarray['minute']) : '' );
    }

    /**
     * @param integer $number
     * @return string
     */
    private function addZeroBefore($number)
    {
        return $number < 10 ? '0' . $number : $number;
    }

    public static function isLeapYear($year)
    {
        return (((((($year - 474) % 2820) + 474) + 38) * 682) % 2816) < 682;
    }

    /**
     * @param \DateTime $miladi
     * @return string
     */
    public static function getWeekDay(\DateTime $miladi = null)
    {
        if ($miladi == null)
            return '';

        switch($miladi->format('w')){
            case 0:
                return '?˜ÔäÈå';
            case 1:
                return 'ÏæÔäÈå';
            case 2:
                return 'Óå ÔäÈå';
            case 3:
                return 'åÇÑ ÔäÈå';
            case 4:
                return 'äÌ ÔäÈå';
            case 5:
                return 'ÌãÚå';
            case 6:
                return 'ÔäÈå';
        }

        return '';
    }
}