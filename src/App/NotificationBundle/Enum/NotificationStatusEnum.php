<?php
/**
 * Created by PhpStorm.
 * User: ivanristic
 * Date: 12/20/16
 * Time: 1:55 PM
 */

namespace App\NotificationBundle\Enum;

class NotificationStatusEnum extends StaticEnum
{
    const PENDING = 'pending';
    const SEEN = 'seen';

    static protected $enum = [
        self::PENDING => 'Pending',
        self::SEEN => 'Seen'
    ];
}