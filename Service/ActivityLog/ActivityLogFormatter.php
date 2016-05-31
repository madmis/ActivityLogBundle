<?php

namespace ActivityLogBundle\Service\ActivityLog;

use ActivityLogBundle\Entity\LogEntry;
use Doctrine\ORM\EntityManager;
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
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param LoggerInterface $logger
     * @param EntityManager $entityManager
     */
    public function __construct(LoggerInterface $logger, EntityManager $entityManager)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
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
     * @param LogEntry $logEntry
     * @return FormatterInterface
     */
    private function getEntryFormatter(LogEntry $logEntry)
    {
        $className = substr(strrchr(rtrim($logEntry->getObjectClass(), '\\'), '\\'), 1);
        $formatterClass = __NAMESPACE__. '\EntityFormatter\\' . $className;

        // Support fully-qualified class names
        $formatter = null;
        if (class_exists($formatterClass)) {
            $implements = in_array('ActivityLogBundle\Service\ActivityLog\EntityFormatter', class_implements($formatterClass), true);
            if ($implements) {
                $formatter = new $formatterClass($this->entityManager);
            }
        }

        if (!$formatter) {
            $this->logger->warning("For entity {$logEntry->getObjectClass()} don't implemented Activity Log Formatter.");
            $formatter = new UniversalFormatter($this->entityManager);
        }

        return $formatter;
    }
}
