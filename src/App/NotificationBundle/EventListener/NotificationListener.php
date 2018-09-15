<?php
/**
 * Created by PhpStorm.
 * User: ivanristic
 * Date: 12/20/16
 * Time: 1:50 PM
 */

namespace App\NotificationBundle\EventListener;

use App\NotificationBundle\Controller\NotificationInterface;
use App\NotificationBundle\Entity\Notification;
use App\NotificationBundle\Enum\NotificationStatusEnum;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class NotificationListener
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof NotificationInterface) {
            $user = $this->container->get('security.context')->getToken()->getUser();
            if ($user && is_object($user)) {
                $notifications = $this->container->get('doctrine')->getRepository(Notification::class)->findBy([
                    'customer' => $user->getId(),
                    'status' => NotificationStatusEnum::PENDING
                ]);
                $em = $this->container->get('doctrine')->getEntityManager();
                foreach ($notifications as $notification) {
                    $this->setFlashMessage($notification);
                    $notification->setStatus(NotificationStatusEnum::SEEN);
                    $em->persist($notification);
                }
                $em->flush();
            }
        }
    }

    private function setFlashMessage(Notification $notification)
    {
        $body = $this->container->get('templating')->render(
            $notification->getTemplate(),
            $notification->getParameters()
        );
        $this->container->get('session')->getFlashBag()->add('shop_notification', $body);
    }
}