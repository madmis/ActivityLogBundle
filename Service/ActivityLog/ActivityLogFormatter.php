<?php

namespace ActivityLogBundle\Service\ActivityLog;

use ActivityLogBundle\Entity\LogEntry;
use ActivityLogBundle\Entity\LogEntryInterface;
use ActivityLogBundle\Service\ActivityLog\EntityFormatter\FormatterInterface;
use ActivityLogBundle\Service\ActivityLog\EntityFormatter\UniversalFormatter;
use Psr\Log\LoggerInterface;

/**
 * Class ActivityLogFormatter
 * @package ActivityLogBundle\Service\ActivityLog
 */
class ActivityLogFormatter
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $customFormatters;

    /**
     * ActivityLogFormatter constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->customFormatters = [];
    }

    /**
     * @param FormatterInterface $formatter
     * @param string $entity
     */
    public function addFormatter($formatter, $entity)
    {
        $implements = in_array(
            'ActivityLogBundle\Service\ActivityLog\EntityFormatter\FormatterInterface',
            class_implements($formatter),
            true
        );

        if ($implements) {
            $this->customFormatters[$entity] = $formatter;
        }
    }

    /**
     * @param array|LogEntry[] $logs
     * @return array
     */
    public function format(array $logs)
    {
        $result = [];
        foreach ($logs as $log) {
            $result[] = $this->getEntryFormatter($log)->format($log);
        }

        return $result;
    }

    /**
     * @param LogEntryInterface|LogEntry $logEntry
     * @return FormatterInterface
     */
    private function getEntryFormatter(LogEntryInterface $logEntry)
    {
        $className = substr(strrchr(rtrim($logEntry->getObjectClass(), '\\'), '\\'), 1);

        $formatter = $this->getCustomFormatter($className);

        if (array_key_exists($className, $this->customFormatters)) {
            $formatter = $this->customFormatters[$className];
        }

        // Support fully-qualified class names
        if (!$formatter) {
            $this->logger->warning("For entity {$logEntry->getObjectClass()} don't implemented Activity Log Formatter.");
            $formatter = new UniversalFormatter();
        }

        return $formatter;
    }

    /**
     * @param string $className
     * @return FormatterInterface|null
     */
    private function getCustomFormatter($className)
    {
        $formatter = null;

        if (array_key_exists($className, $this->customFormatters)) {
            $formatter = $this->customFormatters[$className];
        }

        return $formatter;
    }
}
