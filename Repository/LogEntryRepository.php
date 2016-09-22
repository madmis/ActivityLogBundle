<?php

namespace ActivityLogBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository as BaseRepository;
use Doctrine\ORM\Query;
use Gedmo\Tool\Wrapper\EntityWrapper;

/**
 * Class LogEntryRepository
 * @package ActivityLogBundle\Repository
 */
class LogEntryRepository extends BaseRepository
{
    /**
     * Get the query for loading of log entries
     *
     * @param object $entity
     *
     * @return Query
     */
    public function getLogEntriesQuery($entity)
    {
        $wrapped = new EntityWrapper($entity, $this->_em);
        $objectClass = $wrapped->getMetadata()->name;
        $meta = $this->getClassMetadata();
        $dql = "SELECT log FROM {$meta->name} log";
        $dql .= " WHERE (log.objectId = :objectId AND log.objectClass = :objectClass)";
        $dql .= " OR (log.parentId = :parentId AND log.parentClass = :parentClass)";
        $dql .= " ORDER BY log.version DESC, log.loggedAt ASC";

        $objectId = $wrapped->getIdentifier();
        $q = $this->_em->createQuery($dql);
        $q->setParameters([
            'objectId' => $objectId,
            'objectClass' => $objectClass,
            'parentId' => $objectId,
            'parentClass' => $objectClass,
        ]);

        return $q;
    }

    /**
     * Get the query builder for loading of log entries
     *
     * @param object $entity
     *
     * @return QueryBuilder
     */
    public function getLogEntriesQueryBuilder($entity)
    {
        $wrapped = new EntityWrapper($entity, $this->_em);
        $meta = $this->getClassMetadata();

        $builder = $this->_em->createQueryBuilder();
        $or = $builder->expr()->orX(
            'log.objectId = :objectId AND log.objectClass = :objectClass',
            'log.parentId = :parentId AND log.parentClass = :parentClass'
        );
        $builder->select('log')
            ->from($meta->name, 'log')
            ->andWhere($or)
            ->addOrderBy('log.loggedAt', 'DESC');

        $objectClass = $wrapped->getMetadata()->name;
        $objectId = $wrapped->getIdentifier();
        $builder->setParameters([
            'objectId' => $objectId,
            'objectClass' => $objectClass,
            'parentId' => $objectId,
            'parentClass' => $objectClass,
        ]);

        return $builder;
    }
}
