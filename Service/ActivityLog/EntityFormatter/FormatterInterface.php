<?php

namespace ActivityLogBundle\Service\ActivityLog\EntityFormatter;

use ActivityLogBundle\Entity\LogEntry;

/**
 * Interface FormatterInterface
 * @package ActivityLogBundle\Service\ActivityLog\EntityFormatter
 */
interface FormatterInterface
{
    /**
     * @param LogEntry $log
     * @return array
     */
    public function format(LogEntry $log);
}