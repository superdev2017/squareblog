<?php
namespace App\Common\Symfony\Controller;


trait ControllerTrait
{
    use \Kf\KitBundle\Symfony\Controller\DoctrineORMHelper;
    use \Kf\KitBundle\Symfony\Controller\FlashAlertHelper;
    use \Kf\KitBundle\Symfony\Controller\RequestHelper;

    /**
     * @return \App\UserBundle\Entity\User
     */
    public function getUser()
    {
        return parent::getUser();
    }
}