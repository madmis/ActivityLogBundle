<?php

namespace ActivityLogBundle\Entity\Interfaces;

/**
 * Interface which define that entity has a parent
 *
 * Interface LoggableChildInterface
 * @package ActivityLogBundle\Entity\Interfaces
 */
interface LoggableChildInterface
{
    /**
     * @return object
     */
    public function getParentEntity();
}
