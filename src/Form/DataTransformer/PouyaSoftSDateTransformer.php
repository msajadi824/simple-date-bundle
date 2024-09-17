<?php
namespace PouyaSoft\SDateBundle\Form\DataTransformer;

use PouyaSoft\SDateBundle\Service\jDateService;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class PouyaSoftSDateTransformer implements DataTransformerInterface
{
    private $jDateService;
    private $serverFormat;
    private $locale;
    private $calendar;

    public function __construct(jDateService $jDateService, string $serverFormat, string $locale)
    {
        $this->jDateService = $jDateService;
        $this->serverFormat = $serverFormat;
        $this->locale = $locale;
        $this->calendar = $locale == 'fa' ? 'persian' : 'gregorian';
    }

    public function transform(\DateTime $gDate)
    {
        if($gDate === null) {
            return null;
        }

        if (!$gDate instanceof \DateTime) {
            throw new UnexpectedTypeException($gDate, 'DateTime');
        }

        $result = $this->jDateService->georgianToPersian($gDate, $this->serverFormat, $this->locale, $this->calendar, false);

        if(!$result) {
            throw new TransformationFailedException(intl_get_error_message(), intl_get_error_code());
        }

        return $result;
    }

    public function reverseTransform(string $jDate): ?\DateTime
    {
        if($jDate === null || $jDate === '') {
            return null;
        }

        if (!is_string($jDate)) {
            throw new UnexpectedTypeException($jDate, 'string');
        }

        $result = $this->jDateService->persianToGeorgian($jDate, $this->serverFormat, $this->locale, $this->calendar);

        if(!$result) {
            throw new TransformationFailedException(intl_get_error_message(), intl_get_error_code());
        }

        return $result;
    }
} 