<?php

namespace ActivityLogBundle\Service\ActivityLog\EntityFormatter;

use ActivityLogBundle\Entity\LogEntryInterface;

/**
 * Interface FormatterInterface
 * @package ActivityLogBundle\Service\ActivityLog\EntityFormatter
 */
interface FormatterInterface
{
    /**
     * @param LogEntryInterface $log
     * @return array
     */
    public function format(LogEntryInterface $log);
}
