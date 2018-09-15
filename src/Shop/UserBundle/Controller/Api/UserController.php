<?php

namespace Shop\UserBundle\Controller\Api;

use App\SiteBundle\Entity\Site;
use Shop\UserBundle\Entity\User;
use Shop\UserBundle\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as FW;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    /**
     * Used by Shop to create a user
     *
     * @Rest\View
     * @Rest\Post("/subscription/success")
     * @FW\Security("has_role('ROLE_API_SECRET')")
     */
    public function postSubscriptionSuccessAction(Request $request)
    {
        $subscription_id    = $request->request->get('subscription_id');
        $email              = $request->request->get('email');
        $first_name         = $request->request->get('first_name');
        $last_name          = $request->request->get('last_name');
        $country_code       = $request->request->get('country');
        $campaign_id        = $request->request->get('campaign_id');

        $send_credentials = $request->request->get('send_login_credentials');

        if (empty($subscription_id) || empty($email))
            throw new BadRequestHttpException('M-ID');

        // ASSUME EMAIL IS SAME IN BOTH SYSTEMS
        $userRepo = $this->get('doctrine')->getRepository(User::class);
        $user = $userRepo->findOneBy(['subscriptionId' => $subscription_id]);

        $response = [];

        // If email not exists, create user, with data from service
        if (!$user) {
            $fos_um = $this->get('fos_user.user_manager');
            $um = $this->get('user_manager');

            $old_user = $userRepo->findOneBy(['email' => $email]);
            if ($old_user) {
                // TODO figure something here
                throw new BadRequestHttpException();
            }

            $username = $um->generateValidUsername($first_name, $last_name);

            $full_name = $first_name . ' ' . $last_name;

            $password = UserManager::randomKey(rand(10, 20));

            $role = $um->determineUserRole($request->request->get('subscription_name'));

            $user = $fos_um->createUser();
            $user->setEmail($email)
                ->setUsername($username)
                ->setPlainPassword($password)
                ->setCountryCode($country_code)
                ->addRole($role)
                ->setEnabled(1);

            $encryptedPassword = $this->get('security.password_encoder')
                ->encodePassword($user, $password);
            $user->setPassword($encryptedPassword);
            $user->generateOneTimeLoginToken();

            $this->save($user);

            // Create first site and set messages
            $site = new Site();
            $site->setName($full_name ."'s first website")
                ->setTitle($full_name ."'s first website")
                ->setSlug('')
                ->setDescription('Hello, my name is ' . $full_name . " and this is my first website!")
                ->setTemplate('Blogalicious')
                ->setUser($user);

            $this->save($site);

            if (isset($campaign_id) && in_array($campaign_id, $this->container->getParameter('front_campaign_ids'))) {
                $um = $this->get('user_manager');
                $response['instant_login'] = $um->getInstantLoginURL($user);
            }

            if ($send_credentials) {
                try {
                    $um->sendWelcomeMail($user, $password);
                } catch (\Exception $e) {}
            }
        }
        // We have a user, promote to subscriber if not
        if (!in_array('ROLE_SUBSCRIBER', $user->getRoles())) {
            $user->addRole('ROLE_SUBSCRIBER')
                ->setSubscriptionId($subscription_id);
        }

        $this->save($user);

        return ['status' => 'success', 'data' => $response];
    }

    /**
     * Used by Shop to find a user
     *
     * @Rest\View
     * @Rest\Get("/dispute/data")
     * @FW\Security("has_role('ROLE_API_SECRET')")
     */
    public function disputeDataAction(Request $request) {
        $email = $request->query->get('email');

        $user = $this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user)
            throw new NotFoundHttpException();

        // The user
        $content = "The user account <b>(".$user->getUsername().")</b> which belongs to <b>" . $user->getEmail() ."</b>
         has the following website(s) in our system:<br>";

        $sites = $user->getSites();

        $response = [];
        $response['username'] = $user->getUsername();

        $response['sections'][0] = [
            'headline' => 'BlogCollectio websites operated by ' . $email,
            'reversal_reason' => "As shown in the 'BlogCollectio websites operated by ".$email."' section, this user has at least 1 site at SiteEub.eu"
        ];
        foreach ($sites as $site) {
            $url = 'https:' . $this->get('router')->generate(
                'app_site',
                ['username' => strtolower($user->getUsername()), 'slug' => $site->getSlug()],
                true
            );

            $content .= "<br><b><a target='_blank' href='" . $url . "'>" . $url . "</a></b><br><br>";
            $response['sections'][0]['screens'][] = $url;
        }

        $response['sections'][0]['content'] = $content;

        return $response;
    }

    /**
     * Used by Shop to cancel an account
     *
     * @Rest\View
     * @Rest\QueryParam(name="id", requirements="\d+", nullable=false, description="The subscription id.")
     * @Rest\Post("/subscription/cancel", options={"expose"=true})
     * @FW\Security("has_role('ROLE_CUSTOMER')")
     */
    public function cancelSubscriptionAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if (empty($user))
            throw new BadRequestHttpException();

        $um = $this->container->get('user_manager');
        $response = $um->cancelSubscription($user);

        if (is_object($response) && $response->status == 'success') {
            $user->removeRole('ROLE_SUBSCRIBER');
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($user);
            $em->flush();
            $em->refresh($user);
        }

        return json_encode($response);
    }

    private function delete($obj) {
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($obj);
        $em->flush();
    }

    private function save($obj) {
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($obj);
        $em->flush();
    }
}