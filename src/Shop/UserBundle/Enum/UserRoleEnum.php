<?php

namespace Shop\UserBundle\Enum;

use App\Common\Model\Enum\StaticEnum;

class UserRoleEnum extends StaticEnum
{
    const ROLE_API_SECRET = 'ROLE_API_SECRET';
    const ROLE_API_PUBLIC = 'ROLE_API_PUBLIC';
    const ROLE_USER = 'ROLE_USER';
    const ROLE_CUSTOMER = 'ROLE_CUSTOMER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    static protected $enum = [
        self::ROLE_USER => 'User',
        self::ROLE_CUSTOMER => 'Customer',
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_API_SECRET => 'API Secret',
        self::ROLE_API_PUBLIC => 'API Public'
    ];


    protected static $labelClass = [
        self::ROLE_USER => 'label label-success',
        self::ROLE_ADMIN => 'label label-danger',
    ];

    public static function getLabelClass($key)
    {
        return isset(static::$labelClass[$key]) ? static::$labelClass[$key]
            : 'label label-default';
    }
}
