<?php
namespace PouyaSoft\SDateBundle\Form\DataTransformer;

use PouyaSoft\SDateBundle\Service\jDateService;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class PouyaSoftSDateTransformer implements DataTransformerInterface
{
    /**
     * @var jDateService
     */
    private $jDateService;

    private $serverFormat;

    /**
     * @param jDateService $jDateService
     * @param $serverFormat
     */
    public function __construct(jDateService $jDateService, $serverFormat)
    {
        $this->jDateService = $jDateService;
        $this->serverFormat = $serverFormat;
    }

    /**
     * @param \DateTime $gDate
     * @return string
     */
    public function transform($gDate)
    {
        if($gDate === null) {
            return null;
        }

        if (!$gDate instanceof \DateTime) {
            throw new UnexpectedTypeException($gDate, 'DateTime');
        }

        $result = $this->jDateService->georgianToPersian($gDate, $this->serverFormat);

        if(!$result) {
            throw new TransformationFailedException(intl_get_error_message(), intl_get_error_code());
        }

        return $result;
    }

    /**
     * @param string $jDate
     * @return \DateTime
     */
    public function reverseTransform($jDate)
    {
        if($jDate === null || $jDate === '') {
            return null;
        }

        if (!is_string($jDate)) {
            throw new UnexpectedTypeException($jDate, 'string');
        }

        $result = $this->jDateService->persianToGeorgian($jDate, $this->serverFormat);

        if(!$result) {
            throw new TransformationFailedException(intl_get_error_message(), intl_get_error_code());
        }

        return $result;
    }
} 