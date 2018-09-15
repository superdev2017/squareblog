<?php

namespace App\Common\Doctrine\ORM\Traits;

use Gedmo\Mapping\Annotation as Gedmo;

trait PositionableEntity{

    /**
     * @\Doctrine\ORM\Mapping\Column(type="integer")
     * @Gedmo\SortablePosition
     */
    protected $position;

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }
}