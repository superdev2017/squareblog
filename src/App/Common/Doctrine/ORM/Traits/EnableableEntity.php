<?php

namespace App\Common\Doctrine\ORM\Traits;

trait EnableableEntity{
    /** @\Doctrine\ORM\Mapping\Column(type="boolean") */
    protected $enabled = true;

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }
}