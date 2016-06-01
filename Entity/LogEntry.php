<?php

namespace ActivityLogBundle\Entity;

use ActivityLogBundle\Entity\MappedSuperclass\AbstractLogEntry;
use Doctrine\ORM\Mapping as ORM;
use ActivityLogBundle\Entity\Interfaces\ArrayableInterface;

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
class LogEntry extends AbstractLogEntry
{
}
