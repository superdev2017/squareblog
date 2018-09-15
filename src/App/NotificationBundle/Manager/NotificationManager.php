<?php
/**
 * Created by PhpStorm.
 * User: ivanristic
 * Date: 12/20/16
 * Time: 2:10 PM
 */

namespace App\NotificationBundle\Manager;


use App\NotificationBundle\Entity\Notification;
use App\NotificationBundle\Enum\NotificationTypeEnum;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class NotificationManager
{
    use ContainerAwareTrait;

    public function createNotification($type, $parameters, $priority = 1)
    {
        $path = 'AppNotificationBundle:popup:';

        $template = $path.self::typeToTemplate($type);

        $notification = new Notification($type, $template, $parameters, $priority);

        return $notification;
    }

    private static function typeToTemplate($type) {
        $template = '';

        switch ($type) {
            case NotificationTypeEnum::SUCCESS:
                $template = 'basic\basic.html.twig';
                break;
            case NotificationTypeEnum::ERROR:
                $template = 'basic\basic.html.twig';
                break;
            case NotificationTypeEnum::POINTS_INTRO:
                $template = 'points\intro.html.twig';
                break;
            case NotificationTypeEnum::POINTS_GAINED:
                $template = 'points\gained.html.twig';
                break;
        }

        return $template;
    }
}