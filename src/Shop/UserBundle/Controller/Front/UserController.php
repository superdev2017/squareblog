<?php

namespace Shop\UserBundle\Controller\Front;

use Shop\UserBundle\Entity\User;
use Shop\UserBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;

class UserController extends Controller
{
    /**
     * @Route("/login/token", name="shop_user_login_token")
     */
    public function tokenLoginAction(Request $request)
    {
        $token = $request->query->get('token');
        $username = $request->query->get('username');

        if (empty($token) || empty($username))
            return $this->redirectToRoute('fos_user_security_login');

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'oneTimeLoginToken' => $token,
            'username' => $username
        ]);

        if (!$user)
            return $this->redirectToRoute('fos_user_security_login');


        // All ok, reset token
        $user->setOneTimeLoginToken(null);
        $this->save($user);

        // Here, "public" is the name of the firewall in your security.yml
        $token = new UsernamePasswordToken($user, $user->getPassword(), "main", $user->getRoles());

        // For older versions of Symfony, use security.context here
        $this->get("security.token_storage")->setToken($token);

        // Fire the login event
        // Logging the user in above the way we do it doesn't do this automatically
        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

        return $this->redirectToRoute('light_builder_my_sites');
    }


    /**
     * @Route("/account", name="shop_user_account")
     */
    public function accountAction(Request $request)
    {
        $um = $this->container->get('user_manager');
        $subscription = $um->getSubscription($this->getUser());

        $userForm = $this->createForm(UserType::class);
        $userForm->handleRequest($request);

        if ($request->isMethod('POST')) {
            $data = $request->request->get('user');
            $data['subscription_id'] = $this->getUser()->getSubscriptionId();
            $data['current_email'] = $this->getUser()->getEmail();

            $um = $this->get('user_manager');
            $response = $um->updateCustomer($data);


            if (!is_object($response) || $response->status != 'success') {
                $trans = $this->get('translator');
                $session = $request->getSession();
                $session->getFlashBag()->add('error', $trans->trans('unknown.error.occurred.please.try.again', [], 'pages'));
            }
        }

        $um = $this->get('user_manager');
        $customer = $um->getCustomer($this->getUser());


        return $this->render('user/account.html.twig', [
            'subscription'  => $subscription,
            'customer'      => $customer,
            'userForm'      => $userForm->createView()
        ]);
    }

    /**
     * @Route("/orders", name="shop_user_orders")
     */
    public function ordersAction(Request $request)
    {
        $um = $this->get('user_manager');
        $orders = $um->getOrders($this->getUser());

        return $this->render('user/orders.html.twig', [
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/orders/{id}/invoice", name="shop_user_orders_invoice")
     */
    public function orderInvoiceAction(Request $request, $id)
    {
        $um =           $this->get('user_manager');
        $order =        $um->getOrder($this->getUser(), $id);

        if (!$order) {
            throw new BadRequestHttpException('Order not found with user.');
        }

        $um = $this->get('user_manager');
        $data = $um->getInvoice($order->id);

        if (empty($data)) {
            throw new BadRequestHttpException('Cannot generate invoice at this time.');
        }

        return new Response($data);
    }

    private function save($obj) {
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($obj);
        $em->flush();
    }
}