<?php

namespace Service\ActivityLog\EntityFormatter;


class AbstractFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function getEmMock()
    {
        return $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->getMock();
    }


    public function testNormalizeValue()
    {
        $stub = $this->getMockForAbstractClass(
            'ActivityLogBundle\Service\ActivityLog\EntityFormatter\AbstractFormatter',
            [$this->getEmMock()]
        );

        $result = $stub->normalizeValue('test', ['key' => 'value']);
        $this->assertEquals('key: value;', $result);
        $result = $stub->normalizeValue('test', 'test');
        $this->assertEquals('test', $result);
        $result = $stub->normalizeValue('test', true);
        $this->assertTrue($result);
        $result = $stub->normalizeValue('test', 1);
        $this->assertTrue(is_int($result));
    }
}
