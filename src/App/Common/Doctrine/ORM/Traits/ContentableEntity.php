<?php

namespace App\Common\Doctrine\ORM\Traits;

trait ContentableEntity {

    /** @\Doctrine\ORM\Mapping\Column(type="text", nullable=true) */
    protected $content;

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
}