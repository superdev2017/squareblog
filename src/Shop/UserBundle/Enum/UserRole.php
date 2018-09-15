<?php

namespace Shop\UserBundle\Enum;

class UserRole
{
    private $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function getLabel()
    {
        try {
            return Role::getLabel($this->key);
        }catch(\Exception $e){
            return 'error';
        }
    }

    public function getLabelClass()
    {
        return Role::getLabelClass($this->key);
    }
}