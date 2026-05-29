<?php

namespace PouyaSoft\SDateBundle\Lib;

use DateTime;
use DateTimeZone;
use IntlDateFormatter;
use NumberFormatter;

class IntlDateTime extends DateTime
{
    protected $locale;
    protected $calendar;

    public function __construct(
        $time = null,
        $timezone = null,
        $calendar = 'gregorian',
        $locale = 'en_US',
        $pattern = null
    ) {
        if (!isset($timezone)) {
            $timezone = new DateTimeZone(date_default_timezone_get());
        } elseif (!$timezone instanceof DateTimeZone) {
            $timezone = new DateTimeZone($timezone);
        }

        parent::__construct($time ?: 'now', $timezone);

        $this->locale = $locale;
        $this->calendar = strtolower($calendar);

        if ($time !== null) {
            $this->set($time, null, $pattern);
        }
    }

    protected function getFormatter($options = array())
    {
        $locale = isset($options['locale']) ? $options['locale'] : $this->locale;
        $calendar = isset($options['calendar']) ? $options['calendar'] : $this->calendar;
        $timezone = isset($options['timezone']) ? $options['timezone'] : $this->getTimezone();

        if ($timezone instanceof DateTimeZone) {
            $timezone = $timezone->getName();
        }

        $pattern = isset($options['pattern']) ? $options['pattern'] : null;

        return new IntlDateFormatter(
            $locale . '@calendar=' . $calendar,
            IntlDateFormatter::FULL,
            IntlDateFormatter::FULL,
            $timezone,
            $calendar === 'gregorian'
                ? IntlDateFormatter::GREGORIAN
                : IntlDateFormatter::TRADITIONAL,
            $pattern
        );
    }

    protected function latinizeDigits($str)
    {
        $result = '';
        $num = new NumberFormatter($this->locale, NumberFormatter::DECIMAL);

        preg_match_all('/.[\x80-\xBF]*/', $str, $matches);

        foreach ($matches[0] as $char) {
            $pos = 0;
            $parsed = $num->parse($char, NumberFormatter::TYPE_INT32, $pos);
            $result .= $pos ? $parsed : $char;
        }

        return $result;
    }

    protected function guessPattern($time)
    {
        $time = $this->latinizeDigits(trim($time));

        $shortDateRegex = '(\d{2,4})(-|\\\\|/)\d{1,2}\2\d{1,2}';
        $longDateRegex  = '([^\d]*\s)?\d{1,2}(-| )[^-\s\d]+\4(\d{2,4})';
        $timeRegex      = '\d{1,2}:\d{1,2}(:\d{1,2})?(\s.*)?';

        if (preg_match("@^(?:(?:$shortDateRegex)|(?:$longDateRegex))(\s+$timeRegex)?$@", $time, $match)) {

            if (!empty($match[1])) {
                $separator = $match[2];
                $pattern = (strlen($match[1]) == 2 ? 'yy' : 'yyyy')
                    . $separator . 'MM'
                    . $separator . 'dd';
            } else {
                $separator = isset($match[4]) ? $match[4] : '/';
                $pattern = 'dd' . $separator . 'LLL' . $separator;
                $pattern .= (strlen($match[5] ?? '') == 2 ? 'yy' : 'yyyy');
            }

            if (!empty($match[6])) {
                $pattern .= !empty($match[8]) ? ' hh:mm' : ' HH:mm';

                if (!empty($match[7])) {
                    $pattern .= ':ss';
                }

                if (!empty($match[8])) {
                    $pattern .= ' a';
                }
            }

            return $pattern;
        }

        return false;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setCalendar($calendar)
    {
        $this->calendar = strtolower($calendar);
        return $this;
    }

    public function getCalendar()
    {
        return $this->calendar;
    }

    public function getTimestamp()
    {
        return (int) parent::format('U');
    }

    public function setTimestamp($unixtimestamp)
    {
        $diff = $unixtimestamp - $this->getTimestamp();

        $days = (int) floor($diff / 86400);
        $seconds = $diff - $days * 86400;

        $timezone = $this->getTimezone();

        parent::setTimezone(new DateTimeZone('UTC'));
        parent::modify("$days days $seconds seconds");
        parent::setTimezone($timezone);

        return $this;
    }

    public function set($time, $timezone = null, $pattern = null)
    {
        if ($time instanceof DateTime) {
            $time = $time->format('U');
        }

        if (!is_numeric($time) || $pattern) {

            if (!$pattern) {
                $pattern = $this->guessPattern((string)$time);
            }

            if (
                !$pattern &&
                preg_match('/((?:[+-]?\d+)|next|last|previous)\s*(year|month)s?/i', (string)$time)
            ) {
                $tempTimezone = null;

                if (isset($timezone)) {
                    $tempTimezone = $this->getTimezone();
                    $this->setTimezone($timezone);
                }

                $this->setTimestamp(time());
                $this->modify((string)$time);

                if (isset($timezone)) {
                    $this->setTimezone($tempTimezone);
                }

                return $this;
            }

            $timezone = $timezone ?: $this->getTimezone();

            if ($timezone instanceof DateTimeZone) {
                $timezone = $timezone->getName();
            }

            $oldTz = date_default_timezone_get();
            date_default_timezone_set($timezone);

            if ($pattern) {
                $time = $this->getFormatter(array(
                    'timezone' => 'GMT',
                    'pattern' => $pattern
                ))->format($time);

                $time -= date('Z', $time);
            } else {
                $time = strtotime((string)$time);
            }

            date_default_timezone_set($oldTz);
        }

        $this->setTimestamp((int)$time);

        return $this;
    }

    public function setDate($year, $month, $day)
    {
        return $this->set(
            "$year/$month/$day " . $this->format('H:i:s'),
            null,
            'yyyy/MM/dd HH:mm:ss'
        );
    }

    public function setTimezone($timezone)
    {
        if (!$timezone instanceof DateTimeZone) {
            $timezone = new DateTimeZone($timezone);
        }

        parent::setTimezone($timezone);
        return $this;
    }

    protected function modifyCallback($matches)
    {
        if (!empty($matches[1])) {
            parent::modify($matches[1]);
        }

        list($y, $m, $d) = explode('-', $this->format('Y-m-d'));

        $change = strtolower($matches[2]);

        if ($change === 'next') {
            $change = 1;
        } elseif ($change === 'last' || $change === 'previous') {
            $change = -1;
        } else {
            $change = (int)$change;
        }

        $unit = strtolower($matches[3]);

        if ($unit === 'month') {
            $m += $change;

            if ($m > 12) {
                $y += (int) floor($m / 12);
                $m = $m % 12;
            } elseif ($m < 1) {
                $y += (int) ceil($m / 12) - 1;
                $m = ($m % 12) + 12;
            }
        }

        if ($unit === 'year') {
            $y += $change;
        }

        $this->setDate($y, $m, $d);

        return '';
    }

    public function modify($modify)
    {
        $modify = $this->latinizeDigits(trim($modify));

        $modify = preg_replace_callback(
            '/(.*?)((?:[+-]?\d+)|next|last|previous)\s*(year|month)s?/i',
            array($this, 'modifyCallback'),
            $modify
        );

        if ($modify) {
            parent::modify($modify);
        }

        return $this;
    }

    public function intlFormat($pattern, $timezone = null, $latinizeDigits = false)
    {
        $temp = null;

        if ($timezone !== null) {
            $temp = $this->getTimezone();
            $this->setTimezone($timezone);
        }

        $result = $this->getFormatter(array(
            'timezone' => 'GMT' . parent::format('P'),
            'pattern' => $pattern
        ))->format($this->getTimestamp());

        if ($timezone !== null) {
            $this->setTimezone($temp);
        }

        return $latinizeDigits ? $this->latinizeDigits($result) : $result;
    }
}