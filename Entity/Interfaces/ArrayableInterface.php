<?php

namespace ActivityLogBundle\Entity\Interfaces;

/**
 * Interface ArrayableInterface
 * @package ActivityLogBundle\Entity\Interfaces
 */
interface ArrayableInterface
{
    /**
     * Get object instance as an array.
     * @return array
     */
    public function toArray();
}
