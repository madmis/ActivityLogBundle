<?php

namespace ActivityLogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;
use ActivityLogBundle\Entity\Interfaces\ArrayableInterface;
use ActivityLogBundle\Listener\LoggableListener;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class LogEntry
 * @package ActivityLogBundle\Entity
 * @ORM\Table(
 *     name="log_entries",
 *  indexes={
 *      @ORM\Index(name="log_class_lookup_idx", columns={"object_class"}),
 *      @ORM\Index(name="log_date_lookup_idx", columns={"logged_at"}),
 *      @ORM\Index(name="log_user_lookup_idx", columns={"username"}),
 *      @ORM\Index(name="log_version_lookup_idx", columns={"object_id", "object_class", "version"}),
 *      @ORM\Index(name="log_entries_with_parent_lookup_idx", columns={"object_id", "object_class", "parent_id", "parent_class", "version"})
 *  }
 * )
 * @ORM\Entity(repositoryClass="ActivityLogBundle\Repository\LogEntryRepository")
 */

class LogEntry extends AbstractLogEntry implements LogEntryInterface, ArrayableInterface
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
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
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
