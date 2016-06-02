<?php

namespace Service\ActivityLog;


use ActivityLogBundle\Entity\LogEntry;
use ActivityLogBundle\Service\ActivityLog\ActivityLogFormatter;

class ActivityLogFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testFormat()
    {
        $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')
            ->getMock();
        $logger->method('warning')
        ->willReturn($this->returnValue(null));

        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
        ->getMock();

        $factory = new ActivityLogFormatter($logger, $em, '');
        $logEntry = new LogEntry();
        $logEntry->setOldData(['test' => 'test']);
        $logEntry->setUsername('username');
        $logEntry->setParentClass('AppBundle\Entity\ParentClass');
        $logEntry->setAction('create');
        $logEntry->setName('Name');
        $logEntry->setParentId('parent-id');
        $logEntry->setData(['test' => 'test1']);
        $logEntry->setObjectClass('AppBundle\Entity\ObjectClass');
        $logEntry->setObjectId('object-id');
        $logEntry->setVersion(2);
        $result = $factory->format([$logEntry]);

        $this->assertTrue(is_array($result[0]));
        $this->assertArrayHasKey('message', $result[0]);
        $this->assertEquals('The entity <b>Name (ObjectClass)</b> was created.', $result[0]['message']);
    }
}
