<?php

namespace Service\ActivityLog\EntityFormatter;


class AbstractFormatterTest extends \PHPUnit_Framework_TestCase
{

    public function testNormalizeValue()
    {
        $stub = $this->getMockForAbstractClass(
            'ActivityLogBundle\Service\ActivityLog\EntityFormatter\AbstractFormatter'
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

    /**
     * This test only for coverage - it's not test any real behaviors
     */
    public function testNormalizeValueByMethod()
    {
        $stub = $this->getMockForAbstractClass(
            'ActivityLogBundle\Service\ActivityLog\EntityFormatter\AbstractFormatter',
            [],
            '',
            true,
            true,
            true,
            ['test']
        );
        $stub->method('test')
            ->willReturn('test');

        $result = $stub->normalizeValue('test', 'test');
        $this->assertEquals('test', $result);
    }
}
