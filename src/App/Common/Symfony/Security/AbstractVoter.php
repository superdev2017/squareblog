<?php

namespace App\Common\Symfony\Security;

use App\UserBundle\Entity\User;
use Kf\KitBundle\Symfony\Security\AbstractVoter as BaseAbstractVoter;

abstract class AbstractVoter extends BaseAbstractVoter
{
    const _CREATE = 'CREATE';
    const _VIEW = 'VIEW';
    const _EDIT = 'EDIT';
    const _LIST = 'LIST';
    const _DELETE = 'DELETE';
    const _EXPORT = 'EXPORT';

    static $attributes = [
        self::_CREATE,
        self::_VIEW,
        self::_EDIT,
        self::_DELETE,
        self::_LIST,
        self::_EXPORT
    ];

    /**
     * @return User
     */
    protected function getUser()
    {
        return parent::getUser();
    }
}