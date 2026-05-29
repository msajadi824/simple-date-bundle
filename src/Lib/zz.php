<?php

namespace PouyaSoft\SDateBundle\Lib;

use DateTime;
use DateTimeZone;
use IntlDateFormatter;
use NumberFormatter;

/**
 * Legacy IntlDateTime (PHP 8.2 compatible version)
 */
class IntlDateTime extends DateTime
{
    protected string $locale;
    protected string $calendar;

    public function __construct(
        ?string $time = null,
        DateTimeZone|string|null $timezone = null,
        string $calendar = 'gregorian',
        string $locale = 'en_US',
        ?string $pattern = null
    ) {
        if ($timezone === null) {
            $timezone = new DateTimeZone(date_default_timezone_get());
        } elseif (!$timezone instanceof DateTimeZone) {
            $timezone = new DateTimeZone($timezone);
        }

        parent::__construct($time ?? 'now', $timezone);

        $this->setLocale($locale);
        $this->setCalendar($calendar);

        if ($time !== null) {
            $this->set($time, null, $pattern);
        }
    }

    protected function getFormatter(array $options = []): IntlDateFormatter
    {
        $locale = $options['locale'] ?? $this->locale;
        $calendar = $options['calendar'] ?? $this->calendar;
        $timezone = $options['timezone'] ?? $this->getTimezone();

        if ($timezone instanceof DateTimeZone) {
            $timezone = $timezone->getName();
        }

        $pattern = $options['pattern'] ?? null;

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

    protected function latinizeDigits(string $str): string
    {
        $result = '';
        $num = new NumberFormatter($this->locale, NumberFormatter::DECIMAL);

        preg_match_all('/.[\x80-\xBF]*/', $str, $matches);

        foreach ($matches[0] as $char) {
            $pos = 0;
            $parsed = $num->parse($char, NumberFormatter::TYPE_INT32, $pos);

            $result .= $pos ? (string)$parsed : $char;
        }

        return $result;
    }

    protected function guessPattern(string $time): string|false
    {
        $time = $this->latinizeDigits(trim($time));

        $shortDateRegex = '(\d{2,4})(-|\\\\|/)\d{1,2}\2\d{1,2}';
        $longDateRegex = '([^\d]*\s)?\d{1,2}(-| )[^-\s\d]+\4(\d{2,4})';
        $timeRegex = '\d{1,2}:\d{1,2}(:\d{1,2})?(\s.*)?';

        if (preg_match("@^(?:(?:$shortDateRegex)|(?:$longDateRegex))(\s+$timeRegex)?$@", $time, $match)) {
            if (!empty($match[1])) {
                $separator = $match[2];
                $pattern = strlen($match[1]) === 2 ? 'yy' : 'yyyy';
                $pattern .= $separator . 'MM' . $separator . 'dd';
            } else {
                $separator = $match[4] ?? '/';
                $pattern = 'dd' . $separator . 'LLL' . $separator;
                $pattern .= strlen($match[5] ?? '') === 2 ? 'yy' : 'yyyy';
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

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setCalendar(string $calendar): self
    {
        $this->calendar = strtolower($calendar);
        return $this;
    }

    public function getCalendar(): string
    {
        return $this->calendar;
    }

    public function getTimestamp(): int
    {
        return (int) parent::format('U');
    }

    public function setTimestamp(int $unixtimestamp): static
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

    public function set($time, DateTimeZone|string|null $timezone = null, ?string $pattern = null): self
    {
        if ($time instanceof DateTime) {
            $time = $time->format('U');
        } elseif (!is_numeric($time) || $pattern) {

            if (!$pattern) {
                $pattern = $this->guessPattern((string)$time);
            }

            if (
                !$pattern &&
                preg_match('/((?:[+-]?\d+)|next|last|previous)\s*(year|month)s?/i', (string)$time)
            ) {
                $tempTimezone = null;

                if ($timezone !== null) {
                    $tempTimezone = $this->getTimezone();
                    $this->setTimezone($timezone);
                }

                $this->setTimestamp(time());
                $this->modify((string)$time);

                if ($timezone !== null) {
                    $this->setTimezone($tempTimezone);
                }

                return $this;
            }

            $timezone = $timezone ?? $this->getTimezone();

            if ($timezone instanceof DateTimeZone) {
                $timezone = $timezone->getName();
            }

            $defaultTz = date_default_timezone_get();
            date_default_timezone_set($timezone);

            if ($pattern) {
                $time = $this->getFormatter([
                    'timezone' => 'GMT',
                    'pattern' => $pattern
                ])->format($time);

                $time -= date('Z', $time);
            } else {
                $time = strtotime((string)$time);
            }

            date_default_timezone_set($defaultTz);
        }

        $this->setTimestamp((int)$time);

        return $this;
    }

    public function setDate(int $year, int $month, int $day): static
    {
        $this->set(
            "$year/$month/$day " . $this->intlFormat('HH:mm:ss'),
            null,
            'yyyy/MM/dd HH:mm:ss'
        );

        return $this;
    }

    public function setTimezone(DateTimeZone $timezone): static
    {
        parent::setTimezone($timezone);
        return $this;
    }

    protected function modifyCallback(array $matches): string
    {
        if (!empty($matches[1])) {
            parent::modify($matches[1]);
        }

        [$y, $m, $d] = explode('-', $this->intlFormat('y-M-d'));

        $change = strtolower($matches[2]);
        $unit = strtolower($matches[3]);

        switch ($change) {
			case 'next':
				$change = 1;
				break;

			case 'last':
			case 'previous':
				$change = -1;
				break;

			default:
				$change = (int) $change;
		}

        switch ($unit) {
            case 'month':
                $m += $change;
                if ($m > 12) {
                    $y += (int) floor($m / 12);
                    $m %= 12;
                } elseif ($m < 1) {
                    $y += (int) ceil($m / 12) - 1;
                    $m = ($m % 12) + 12;
                }
                break;

            case 'year':
                $y += $change;
                break;
        }

        $this->setDate($y, $m, $d);

        return '';
    }

    public function modify(string $modify): static
    {
        $modify = $this->latinizeDigits(trim($modify));

        $modify = preg_replace_callback(
            '/(.*?)((?:[+-]?\d+)|next|last|previous)\s*(year|month)s?/i',
            [$this, 'modifyCallback'],
            $modify
        );

        if ($modify) {
            parent::modify($modify);
        }

        return $this;
    }

    public function intlFormat(string $pattern, DateTimeZone|string|null $timezone = null, bool $latinizeDigits = false): string
    {
        $tempTimezone = null;

        if ($timezone !== null) {
            $tempTimezone = $this->getTimezone();
            $this->setTimezone($timezone);
        }

        $result = $this->getFormatter([
            'timezone' => 'GMT' . (parent::format('Z') ? parent::format('P') : ''),
            'pattern' => $pattern
        ])->format($this->getTimestamp());

        if ($timezone !== null) {
            $this->setTimezone($tempTimezone);
        }

        return $latinizeDigits ? $this->latinizeDigits($result) : $result;
    }
}