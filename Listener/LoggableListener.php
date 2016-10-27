<?php

namespace ActivityLogBundle\Listener;

use Doctrine\ORM\UnitOfWork;
use \Gedmo\Loggable\LoggableListener as BaseListener;
use Doctrine\Common\EventArgs;
use Gedmo\Mapping\Event\AdapterInterface;
use Gedmo\Tool\Wrapper\AbstractWrapper;
use ActivityLogBundle\Entity\Interfaces\LoggableChildInterface;
use ActivityLogBundle\Entity\Interfaces\StringableInterface;
use ActivityLogBundle\Entity\LogEntryInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class LoggableListener
 * @package ActivityLogBundle\Listener
 */
class LoggableListener extends BaseListener
{
    /**
     * @var AdapterInterface
     */
    protected $eventAdapter;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * Set username for identification
     *
     * @param mixed $username
     *
     * @throws \Gedmo\Exception\InvalidArgumentException Invalid username
     */
    public function setUsername($username)
    {
        if ($username instanceof TokenInterface
            && $username->getUser() instanceof UserInterface) {
            $this->user = $username->getUser();
        }

        parent::setUsername($username);
    }

    /**
     * Looks for loggable objects being inserted or updated
     * for further processing
     *
     * @param EventArgs $eventArgs
     *
     * @return void
     */
    public function onFlush(EventArgs $eventArgs)
    {
        $this->eventAdapter = $this->getEventAdapter($eventArgs);

        parent::onFlush($eventArgs);
    }

    /**
     * Handle any custom LogEntry functionality that needs to be performed
     * before persisting it
     *
     * @param LogEntryInterface $logEntry The LogEntry being persisted
     * @param object $object   The object being Logged
     */
    protected function prePersistLogEntry($logEntry, $object)
    {
        if ($this->user instanceof UserInterface) {
            $logEntry->setUser($this->user);
        }

        if ($object instanceof StringableInterface) {
            $logEntry->setName($object->toString());
        } else {
            $logEntry->setName($logEntry->getObjectId());
        }

        if ($this->eventAdapter) {
            $om = $this->eventAdapter->getObjectManager();
            /** @var UnitOfWork $uow */
            $uow = $om->getUnitOfWork();
            $wrapped = AbstractWrapper::wrap($object, $om);
            $meta = $wrapped->getMetadata();
            $config = $this->getConfiguration($om, $meta->name);

            if ($logEntry->getOldData() === null) {
                // save relations to parent entity
                if ($object instanceof LoggableChildInterface && $object->getParentEntity() !== null) {
                    $parent = $object->getParentEntity();
                    $parentMeta = AbstractWrapper::wrap($parent, $om)->getMetadata();
                    $logEntry->setParentId($parent->getId());
                    $logEntry->setParentClass($parentMeta->name);
                }

                // don't save old data for new entity,
                // because this data duplicate new data
                if ($logEntry->isCreate()) {
                    return;
                }

                $oldValues = [];
                $changeSet = $uow->getEntityChangeSet($object);
                foreach ($changeSet as $field => $changes) {
                    if (empty($config['versioned']) || !in_array($field, $config['versioned'], true)) {
                        continue;
                    }

                    if (!array_key_exists(0, $changes)) {
                        continue;
                    }
                    $value = $changes[0];

                    if ($value && $meta->isSingleValuedAssociation($field)) {
                        if ($wrapped->isEmbeddedAssociation($field)) {
                            $value = $this->getObjectChangeSetData($this->eventAdapter, $value, $logEntry);
                        } else {
                            $wrappedAssoc = AbstractWrapper::wrap($value, $om);
                            $value = $wrappedAssoc->getIdentifier(false);
                            if (!is_array($value) && !$value) {
                                continue;
                            }
                        }
                    }

                    $oldValues[$field] = $value;
                }

                if ($oldValues) {
                    $logEntry->setOldData($oldValues);
                }
            }
        }
    }
}
