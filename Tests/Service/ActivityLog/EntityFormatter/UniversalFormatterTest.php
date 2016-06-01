<?php

namespace ActivityLogBundle\Tests\Service\ActivityLog\EntityFormatter;

use ActivityLogBundle\Entity\LogEntry;
use ActivityLogBundle\Listener\LoggableListener;
use ActivityLogBundle\Service\ActivityLog\EntityFormatter\UniversalFormatter;
use Doctrine\ORM\EntityManager;

class UniversalFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testFormatCreate()
    {
        $entry = new LogEntry();
        $entry->setName('name 1');
        $entry->setAction(LoggableListener::ACTION_CREATE);
        $entry->setData(['testProperty' => true]);
        $entry->setObjectClass('AppBundle\Entity\Project');

        $formatter = new UniversalFormatter($this->getEmMock());
        $result = $formatter->format($entry);

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('The entity <b>name 1 (Project)</b> was created.', $result['message']);
    }

    public function testFormatUpdate()
    {
        $entry = new LogEntry();
        $entry->setName('name 1');
        $entry->setAction(LoggableListener::ACTION_UPDATE);
        $entry->setData(['testProperty' => true]);
        $entry->setOldData(['testProperty' => false]);
        $entry->setObjectClass('AppBundle\Entity\Project');

        $formatter = new UniversalFormatter($this->getEmMock());
        $result = $formatter->format($entry);

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('message', $result);
        $this->assertContains('The entity <b>name 1 (Project)</b> was updated.', $result['message']);
    }

    public function testFormatRemove()
    {
        $entry = new LogEntry();
        $entry->setName('name 1');
        $entry->setAction(LoggableListener::ACTION_REMOVE);
        $entry->setData(['testProperty' => true]);
        $entry->setObjectClass('AppBundle\Entity\Project');

        $formatter = new UniversalFormatter($this->getEmMock());
        $result = $formatter->format($entry);

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('The entity <b>name 1 (Project)</b> was removed.', $result['message']);

    }

    public function testFormatInvalidAction()
    {
        $entry = new LogEntry();
        $entry->setName('name 1');
        $entry->setAction('invalid action');
        $entry->setData(['testProperty' => true]);
        $entry->setObjectClass('AppBundle\Entity\Project');

        $formatter = new UniversalFormatter($this->getEmMock());
        $result = $formatter->format($entry);

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('Undefined action: invalid action.', $result['message']);
    }

    public function getEmMock()
    {
        return $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->getMock();
    }
}