<?php

namespace PouyaSoft\SDateBundle\Lib;

class IntlDateTime extends \DateTime {

	protected $locale;

	protected $calendar;

	public function __construct($time = null, $timezone = null, $calendar = 'gregorian', $locale = 'en_US', $pattern = null) {
		if (!isset($timezone)) $timezone = new \DateTimeZone(date_default_timezone_get());
		elseif (!($timezone instanceof \DateTimeZone)) $timezone = new \DateTimeZone($timezone);

		parent::__construct(null, $timezone);

		$this->setLocale($locale);
		$this->setCalendar($calendar);

		if (isset($time)) $this->set($time, null, $pattern);
	}

	protected function getFormatter($options = array()) {
		$locale = empty($options['locale']) ? $this->locale : $options['locale'];
		$calendar = empty($options['calendar']) ? $this->calendar : $options['calendar'];
		$timezone = empty($options['timezone']) ? $this->getTimezone() : $options['timezone'];
		if ($timezone instanceof \DateTimeZone) $timezone = $timezone->getName();
		$pattern = empty($options['pattern']) ? null : $options['pattern'];

		return new \IntlDateFormatter(
			$locale . '@calendar=' . $calendar,
			\IntlDateFormatter::FULL,
			\IntlDateFormatter::FULL,
			$timezone,
			$calendar == 'gregorian' ? \IntlDateFormatter::GREGORIAN : \IntlDateFormatter::TRADITIONAL,
			$pattern
		);
	}

	protected function latinizeDigits($str) {
		$result = '';
		$num = new \NumberFormatter($this->locale, \NumberFormatter::DECIMAL);
		preg_match_all('/.[\x80-\xBF]*/', $str, $matches);

		foreach ($matches[0] as $char) {
			$pos = 0;
			$parsedChar = $num->parse($char, \NumberFormatter::TYPE_INT32, $pos);
			$result .= $pos ? $parsedChar : $char;
		}

		return $result;
	}

	protected function guessPattern($time) {
		$time = $this->latinizeDigits(trim($time));

		$shortDateRegex = '(\d{2,4})(-|\\\\|/)\d{1,2}\2\d{1,2}';
		$longDateRegex = '([^\d]*\s)?\d{1,2}(-| )[^-\s\d]+\4(\d{2,4})';
		$timeRegex = '\d{1,2}:\d{1,2}(:\d{1,2})?(\s.*)?';

		if (preg_match("@^(?:(?:$shortDateRegex)|(?:$longDateRegex))(\s+$timeRegex)?$@", $time, $match)) {
			if (!empty($match[1])) {
				$separator = $match[2];
				$pattern = strlen($match[1]) == 2 ? 'yy' : 'yyyy';
				$pattern .= $separator . 'MM' . $separator . 'dd';
			} else {
				$separator = $match[4];
				$pattern = 'dd' . $separator . 'LLL' . $separator;
				$pattern .= strlen($match[5]) == 2 ? 'yy' : 'yyyy';
				if (!empty($match[3])) $pattern = (preg_match('/,\s+$/', $match[3]) ? 'E, ' : 'E ') . $pattern;
			}

			if (!empty($match[6])) {
				$pattern .= !empty($match[8]) ? ' hh:mm' : ' HH:mm';
				if (!empty($match[7])) $pattern .= ':ss';
				if (!empty($match[8])) $pattern .= ' a';
			}

			return $pattern;
		}

		return false;
	}

	public function setLocale($locale) {
		$this->locale = $locale;
		return $this;
	}

	public function getLocale() {
		return $this->locale;
	}

	public function setCalendar($calendar) {
		$this->calendar = strtolower($calendar);
		return $this;
	}

	public function getCalendar() {
		return $this->calendar;
	}

	public function getTimestamp() {
		return floatval(parent::format('U'));
	}

	public function setTimestamp($unixtimestamp) {
		$diff = $unixtimestamp - $this->getTimestamp();
		$days = floor($diff / 86400);
		$seconds = $diff - $days * 86400;
		$timezone = $this->getTimezone();
		$this->setTimezone('UTC');
		parent::modify("$days days $seconds seconds");
		$this->setTimezone($timezone);
		return $this;
	}

	public function set($time, $timezone = null, $pattern = null) {
		if ($time instanceof \DateTime) {
			$time = $time->format('U');
		} elseif (!is_numeric($time) || $pattern) {
			if (!$pattern) {
				$pattern = $this->guessPattern($time);
			}

			if (!$pattern && preg_match('/((?:[+-]?\d+)|next|last|previous)\s*(year|month)s?/i', $time)) {
                $tempTimezone = null;

				if (isset($timezone)) {
					$tempTimezone = $this->getTimezone();
					$this->setTimezone($timezone);
				}

				$this->setTimestamp(time());
				$this->modify($time);

				if (isset($timezone)) {
					$this->setTimezone($tempTimezone);
				}

				return $this;
			}

			$timezone = empty($timezone) ? $this->getTimezone() : $timezone;
			if ($timezone instanceof \DateTimeZone) $timezone = $timezone->getName();
			$defaultTimezone = date_default_timezone_get();
			date_default_timezone_set($timezone);

			if ($pattern) {
				$time = $this->getFormatter(array('timezone' => 'GMT', 'pattern' => $pattern))->parse($time);
				$time -= date('Z', $time);
			} else {
				$time = strtotime($time);
			}

			date_default_timezone_set($defaultTimezone);
		}

		$this->setTimestamp($time);

		return $this;
	}

	public function setDate($year, $month, $day) {
		$this->set("$year/$month/$day ".$this->intlFormat('HH:mm:ss'), null, 'yyyy/MM/dd HH:mm:ss');
		return $this;
	}

	public function setTimezone($timezone) {
		if (!($timezone instanceof \DateTimeZone)) $timezone = new \DateTimeZone($timezone);
		parent::setTimezone($timezone);
		return $this;
	}

	protected function modifyCallback($matches) {
		if (!empty($matches[1])) {
			parent::modify($matches[1]);
		}

		list($y, $m, $d) = explode('-', $this->intlFormat('y-M-d'));
		$change = strtolower($matches[2]);
		$unit = strtolower($matches[3]);

		switch ($change) {
			case "next":
				$change = 1;
				break;

			case "last":
			case "previous":
				$change = -1;
				break;
		}

		switch ($unit) {
			case "month":
				$m += $change;
				if ($m > 12) {
					$y += floor($m/12);
					$m = $m % 12;
				} elseif ($m < 1) {
					$y += ceil($m/12) - 1;
					$m = $m % 12 + 12;
				}
				break;

			case "year":
				$y += $change;
				break;
		}

		$this->setDate($y, $m, $d);

		return '';
	}

	public function modify($modify) {
		$modify = $this->latinizeDigits(trim($modify));
		$modify = preg_replace_callback('/(.*?)((?:[+-]?\d+)|next|last|previous)\s*(year|month)s?/i', array($this, 'modifyCallback'), $modify);
		if ($modify) parent::modify($modify);
		return $this;
	}

	public function intlFormat($pattern, $timezone = null, $latinizeDigits = false) {
        $tempTimezone = null;

        if (isset($timezone)) {
			$tempTimezone = $this->getTimezone();
			$this->setTimezone($timezone);
		}

		// Timezones DST data in ICU are not as accurate as PHP.
		// So we get timezone offset from php and pass it to ICU.
		$result = $this->getFormatter(array(
			'timezone' => 'GMT' . (parent::format('Z') ? parent::format('P') : ''),
			'pattern' => $pattern
		))->format($this->getTimestamp());

		if (isset($timezone)) {
			$this->setTimezone($tempTimezone);
		}

		return $latinizeDigits ? $this->latinizeDigits($result) : $result;
	}
}
