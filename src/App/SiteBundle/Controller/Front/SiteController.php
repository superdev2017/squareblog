<?php

namespace App\SiteBundle\Controller\Front;

use Shop\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SiteController extends Controller
{
    public function indexAction(Request $request, $username, $slug)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user)
            throw new BadRequestHttpException('User was not found.');

        $um = $this->get('user_manager');
        $customer = $um->getCustomer($user);

        $slugSite = null;
        foreach ($user->getSites() as $site) {
            if ($site->getSlug() == $slug) {
                $slugSite = $site;
            }
        }

        if (!$slugSite)
            throw new BadRequestHttpException('Slug: ' . $slug . ' not found.');

        return $this->render('AppSiteBundle:Template/'.$site->getTemplate().':index.html.twig', [
            'user'     => $user,
            'customer' => $customer,
            'site'     => $slugSite
        ]);
    }

}
