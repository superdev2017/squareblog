<?php

namespace App\Common\Doctrine\ORM\Traits;

trait NameableEntity
{
    /**
     * @var string
     *
     * @\Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $name;


    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
} 
