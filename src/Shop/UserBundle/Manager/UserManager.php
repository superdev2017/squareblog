<?php

namespace Shop\UserBundle\Manager;

use Mailgun\Mailgun;
use PropelConversions\PropelConversions;
use Shop\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserManager {

    use ContainerAwareTrait;

    private $pc;

    private $customer;
    private $subscription;
    private $orders;

    public function __construct($domain, $secret)
    {
        $this->pc = new PropelConversions($domain, $secret);
    }

    public function getSubscription(User $user)
    {
        if ($this->subscription)
            return $this->subscription;

        $this->subscription = $this->pc->getSubscription($user->getSubscriptionId());;

        return $this->subscription;
    }

    public function cancelSubscription(User $user)
    {
        $response = $this->pc->cancelSubscription($user->getSubscriptionId(), $user->getEmail());
        return $response;
    }

    public function getCustomer(User $user)
    {
        if ($this->customer)
            return $this->customer;

        $subscription = $this->getSubscription($user);

        $this->customer = $this->pc->getCustomer($subscription->customer_id);

        return $this->customer;
    }

    public function updateCustomer($data)
    {
        return $this->pc->updateCustomer($data);
    }

    public function getOrders(User $user)
    {
        if ($this->orders)
            return $this->orders;

        $subscription = $this->getSubscription($user);

        return $this->pc->getOrders($subscription->customer_id);
    }

    public function getOrder(User $user, $id) {
        $orders = $this->getOrders($user);
        $selected_order = null;
        foreach ($orders as $order) {
            if ($order->id == $id) {
                $selected_order = $order;
            }
        }
        return $selected_order;
    }

    public function getInvoice($orderId)
    {
        return $this->pc->getInvoice($orderId);
    }

    public function sendWelcomeMail(User $user, $password = null) {
        $locale = $this->setLocaleFromUser($user);

        $login_url = $this->container->get('router')->generate('homepage', [
            'email'     => $user->getEmail(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $site_url = $this->container->get('router')->generate('app_site', [
            'username' => urlencode($user->getUsername())
        ], UrlGeneratorInterface::ABSOLUTE_URL);


        $terms_url = $this->container->get('router')->generate('terms', [
            'iframe' => 1
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $body = $this->container->get('templating')->render(
            'ShopUserBundle:email:User\welcome_feb_2.html.twig',
            [
                'user'      => $user,
                'password'  => $password,
                'login_url' => $login_url,
                'site_url'  => $site_url
            ]
        );

        $mg = new Mailgun($this->container->getParameter('mailgun_privatekey'));
        $mg->sendMessage($this->container->getParameter('mailgun_domain'), [
                'from'    => $this->container->getParameter('support_email'),
                'to'      => $user->getEmail(),
                'subject' => $this->container->getParameter('app_name') . ' - ' . $user->getUsername(),
                'html'    => $body
            ]
        );
    }

    public function getInstantLoginURL($user) {
        $login_url = $this->container->get('router')->generate('shop_user_login_token', [
            'token'     => urlencode($user->getOneTimeLoginToken()),
            'username'  => urlencode($user->getUsername())
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        return $login_url;
    }

    public function setLocaleFromUser(User $user)
    {

        $locale = 'en'; // Default
        foreach ($this->container->getParameter('supported_countries') as $key => $country) {
            if ($country['country_code'] == $user->getCountryCode()) {
                $locale = $country['locale'];
            }
        }

        $this->container->get('translator')->setLocale($locale);

        return $locale;
    }

    public function generateValidUsername($firstName, $lastName) {

        $loop_usernames = true;
        $i = 0;
        while ($loop_usernames) {
            $suffix = null;
            if ($i !== 0) {
                $suffix = '-' . $i;
            }

            $username = $firstName . '-' .$lastName . $suffix;

            if ($this->checkUsernameValid($username)) {
                $loop_usernames = false;
                break;
            }

            $i++;
        }

        return $username;
    }

    public function determineUserRole($subscriptionName) {

        $role = 'ROLE_CUSTOMER';

        if ($subscriptionName == 'Advanced') {
            return 'ROLE_PRO';
        } else {
            return 'ROLE_BASIC';
        }
    }

    private function checkUsernameValid($username) {
        $userRepo = $this->container->get('doctrine')->getRepository(User::class);
        $user = $userRepo->findOneBy(['username' => $username]);
        if ($user) {
            return false;
        }
        return true;
    }

    public static function randomKey($length)
    {
        $key = '';
        $pool = array_merge(range(0,9), range('a', 'z'),range('A', 'Z'));

        for($i=0; $i < $length; $i++) {
            $key .= $pool[mt_rand(0, count($pool) - 1)];
        }
        return $key;
    }
}