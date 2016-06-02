<?php


namespace Entity\MappedSuperclass;


use ActivityLogBundle\Entity\MappedSuperclass\AbstractLogEntry;

class AbstractLogEntryTest extends \PHPUnit_Framework_TestCase
{
    public function testSetParentId()
    {
        $entity = $this->getEntityMock();
        $entity->setParentId('parent-id');
        $this->assertEquals('parent-id', $entity->getParentId());
    }

    public function testSetParentClass()
    {
        $entity = $this->getEntityMock();
        $entity->setParentClass('ParentClass');
        $this->assertEquals('ParentClass', $entity->getParentClass());
    }

    public function testSetUser()
    {
        $user = $this->getMockBuilder('Symfony\Component\Security\Core\User\UserInterface')
            ->getMock();
        $entity = $this->getEntityMock();
        $entity->setUser($user);
        $this->assertInstanceOf(
            'Symfony\Component\Security\Core\User\UserInterface',
            $entity->getUser()
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AbstractLogEntry
     */
    private function getEntityMock() {
        return $this->getMockForAbstractClass(
            'ActivityLogBundle\Entity\MappedSuperclass\AbstractLogEntry'
        );
    }
}
