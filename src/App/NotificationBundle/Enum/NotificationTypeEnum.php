<?php
/**
 * Created by PhpStorm.
 * User: ivanristic
 * Date: 12/20/16
 * Time: 1:55 PM
 */

namespace App\NotificationBundle\Enum;

class NotificationTypeEnum extends StaticEnum
{
    const ERROR = 'error';
    const WARNING = 'warning';
    const INFO = 'info';
    const SUCCESS = 'success';

    static protected $enum = [
        self::ERROR => 'Error',
        self::WARNING => 'Warning',
        self::INFO => 'Info',
        self::SUCCESS => 'Success',
    ];
}