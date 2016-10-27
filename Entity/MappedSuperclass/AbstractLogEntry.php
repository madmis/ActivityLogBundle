<?php

namespace ActivityLogBundle\Entity\MappedSuperclass;

use ActivityLogBundle\Entity\Interfaces\ArrayableInterface;
use ActivityLogBundle\Entity\LogEntryInterface;
use Doctrine\ORM\Mapping as ORM;
use ActivityLogBundle\Listener\LoggableListener;
use Symfony\Component\Security\Core\User\UserInterface;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry as GedmoEtnry;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractLogEntry extends GedmoEtnry implements LogEntryInterface, ArrayableInterface
{
    /**
     * @var string
     * @ORM\Column(name="parent_id", length=64, nullable=true)
     */
    protected $parentId;

    /**
     * @var string
     * @ORM\Column(name="parent_class", type="string", length=255, nullable=true)
     */
    protected $parentClass;

    /**
     * @var array
     * @ORM\Column(type="array", nullable=true)
     */
    protected $oldData = null;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var UserInterface
     * @ORM\ManyToOne(targetEntity="Symfony\Component\Security\Core\User\UserInterface")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    protected $user;

    /**
     * @return string
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param string $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * @return string
     */
    public function getParentClass()
    {
        return $this->parentClass;
    }

    /**
     * @param string $parentClass
     */
    public function setParentClass($parentClass)
    {
        $this->parentClass = $parentClass;
    }

    /**
     * @return array
     */
    public function getOldData()
    {
        return $this->oldData;
    }

    /**
     * @param array $oldData
     */
    public function setOldData(array $oldData)
    {
        $this->oldData = $oldData;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return UserInterface|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserInterface|null $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Is action CREATE
     * @return bool
     */
    public function isCreate()
    {
        return $this->getAction() === LoggableListener::ACTION_CREATE;
    }

    /**
     * Is action UPDATE
     * @return bool
     */
    public function isUpdate()
    {
        return $this->getAction() === LoggableListener::ACTION_UPDATE;
    }

    /**
     * Is action DELETE
     * @return bool
     */
    public function isRemove()
    {
        return $this->getAction() === LoggableListener::ACTION_REMOVE;
    }

    /**
     * Get object instance as an array.
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'data' => $this->getData(),
            'oldData' => $this->getOldData(),
            'objectClass' => $this->getObjectClass(),
            'objectId' => $this->getObjectId(),
            'parentClass' => $this->getParentClass(),
            'parentId' => $this->getParentId(),
            'action' => $this->getAction(),
            'username' => $this->getUsername(),
            'user' => $this->getUser(),
            'loggedAt' => $this->getLoggedAt(),
            'version' => $this->getVersion(),
        ];
    }
}
