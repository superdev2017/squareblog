<?php

namespace App\CoreBundle\Controller\Front;

use App\SiteBundle\Entity\Site;
use IMCreator\IMCreator;
use Mailgun\Mailgun;
use PropelConversions\PropelConversions;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use GuzzleHttp\Client;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;


class PagesController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        // replace this example code with whatever you need
        return $this->render('pages/index.html.twig', [
            'error' => $this->getLoginError($request)
        ]);
    }

    /**
     * @Route("/buy/{id}", name="buy")
     */
    public function buyAction(Request $request, $id) {

        $frame = 'https://buy.easywebbuilder.eu/en/c/easy-web-builder-basic';
        if ($id == 2) {
            $frame = 'https://buy.easywebbuilder.eu/en/c/easy-web-builder-pro';
        }

        return $this->render('pages/buy.html.twig', [
            'frame' => $frame
        ]);
    }

    /**
     * @Route("/our-services", name="ourservice")
     */
    public function ourserviceAction(Request $request) {

        return $this->render('pages/service.html.twig', [
            'error' => $this->getLoginError($request)
        ]);
    }

    /**
     * @Route("/bill", name="charge")
     */
    public function chargeAction(Request $request) {

        return $this->render('pages/charge.html.twig', [
            'error' => $this->getLoginError($request)
        ]);
    }

    /**
     * @Route("/b", name="bill_first")
     */
    public function billFirstAction(Request $request) {

        return $this->render('pages/charge.html.twig', [
            'error' => $this->getLoginError($request)
        ]);
    }

    /**
     * @Route("/charged", name="bill")
     */
    public function billAction(Request $request) {

        return $this->chargeAction($request);
    }

    /**
     * @Route("/paid", name="paid")
     */
    public function paidAction(Request $request) {

        return $this->chargeAction($request);
    }

    /**
     * @Route("/job/{slug}", name="job")
     */
    public function jobsAction(Request $request, $slug) {

        $job = $this->getDoctrine()->getRepository('AppCoreBundle:Job')->findOneBy([
            'slug' => $slug
        ]);

        if (!$job) {
            throw new BadRequestHttpException('Job not found.');
        }

        // technical-support-advisor
        // java-php-developer
        // social-media-manager
        // seo-marketing-analyst

        return $this->render('pages/job.html.twig', [
            'job' => $job,
            'error' => $this->getLoginError($request)
        ]);
    }

    /**
     * @Route("/templates-overview", name="templates_overview")
     */
    public function templatesOverviewAction(Request $request) {

        return $this->render('pages/templates.html.twig', [
            'error' => $this->getLoginError($request)
        ]);
    }

    /**
     * @Route("/about-us", name="about_us")
     */
    public function aboutUsAction(Request $request) {

        return $this->render('pages/about_us.html.twig', [
            'error' => $this->getLoginError($request)
        ]);
    }

    /**
     * @Route("/affiliate", name="affiliate")
     */
    public function affiliatsAction(Request $request) {

        return $this->render('pages/affiliate.html.twig', [
            'error' => $this->getLoginError($request)
        ]);
    }


    /**
     * @Route("/terms-conditions", name="terms")
     */
    public function termsAction(Request $request) {
        $iframe = false;
        if ($request->query->get('iframe')) {
            $iframe = true;
        }

        return $this->render('pages/terms.html.twig', [
            'iframe' => $iframe,
            'error' => $this->getLoginError($request)
        ]);
    }

    /**
     * @Route("/how-it-works", name="how-it-works")
     */
    public function howItWorksAction(Request $request) {
        return $this->render('pages/how-it-works.html.twig', [
            'error' => $this->getLoginError($request)
        ]);
    }

    /**
     * @Route("/return", name="return")
     */
    public function returnAction(Request $request) {
        $iframe = false;
        if ($request->query->get('iframe')) {
            $iframe = true;
        }

        return $this->render('pages/return.html.twig', [
            'iframe' => $iframe,
            'error' => $this->getLoginError($request)
        ]);
    }

    /**
     * @Route("/privacy", name="privacy")
     */
    public function privacyAction(Request $request) {
        $iframe = false;
        if ($request->query->get('iframe')) {
            $iframe = true;
        }

        return $this->render('pages/privacy.html.twig', [
            'iframe' => $iframe,
            'error' => $this->getLoginError($request)
        ]);
    }

    /**
     * @Route("/advanced-builder", name="advanced_builder")
     */
    public function advancedBuilderAction() {

        $username = strtolower($this->getUser()->getUsername());
        $email = strtolower($this->getUser()->getEmail());

        $username = preg_replace('/[^\00-\255]+/u', '', $username);

        $imcreator = new IMCreator($this->getParameter('im_label'), $this->getParameter('im_apikey'));

        $response = $imcreator->createUser($username, $email, $username);

        /*
        if ($response == 201) {
            $um = $this->get('user_manager');
            $subscription = $um->getSubscription($this->getUser());

            $default_theme = 'vbid-6f479-hbby48pa';
            // Is first create, create site
            $response = $imcreator->createSite($username, $default_theme);

            $siteId = $response->SITE_ID;

            $response = $imcreator->createLicense($username, $default_theme, $username, $siteId, $subscription->name, $subscription->id);

            $licenseId = $response->LICENSE_ID;

            // Publish site
            $response = $imcreator->publishSite($username, $licenseId);

            print_r($response); die();
        }
        */

        $url = $imcreator->authenticateUser($username, $username);

        return $this->redirect($url);

        return $this->render('site_builder/builder.html.twig', [
            'iframe' => $url
        ]);
    }

    /**
     * @Route("/light-builder/edit/{id}", name="light_builder_edit")
     */
    public function lightBuilderEditAction($id) {

        $sites = $this->getUser()->getSites();

        $editSite = null;
        foreach ($sites as $site) {
            if ($site->getId() == $id) {
                $editSite = $site;
            }
        }

        if (!$editSite)
            throw new BadRequestHttpException('This site does not belong to you.');

        $url = $this->get('router')->generate(
            'app_site',
            [
                'username'      => $this->getUser()->getUsername(),
                'app_domain'    => $this->getParameter('app_domain'),
                'api_key'       => $this->getUser()->getApiKey(),
                'slug'          => $editSite->getSlug()
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->render('site_builder/builder.html.twig', [
            'iframe' => $url
        ]);
    }

    /**
     * @Route("/light-builder/my-sites", name="light_builder_my_sites")
     */
    public function lightBuilderMySitesAction() {

        $playIntro = false;
        if ($this->getUser()->getIntro() == false) {
            $this->getUser()->setIntro(true);
            $this->save($this->getUser());
            $playIntro = true;
        }

        $um = $this->get('user_manager');
        $customer = $um->getCustomer($this->getUser());

        $sites = $this->getUser()->getSites();

        $templates = [
            'Blogalicious' => 'One Page Love'
        ];

        return $this->render('site_builder/my_sites.html.twig', [
            'customer'  => $customer,
            'sites'     => $sites,
            'templates' => $templates,
            'playIntro' => $playIntro
        ]);
    }

    /**
     * @Route("/light-builder/view-site/{id}", name="light_builder_view_site")
     */
    public function lightBuilderViewSite($id) {
        $sites = $this->getUser()->getSites();

        $chosen = null;
        foreach ($sites as $site) {
            if ($site->getId() == $id) {
                $chosen = $site;
            }
        }

        if (!$chosen) {
            throw new BadRequestHttpException('This site has not been shared with you, please visit it via the slug');
        }

        return $this->redirectToRoute('app_site', [
            'slug'      => $chosen->getSlug(),
            'username'  => $this->getUser()->getUsername()
        ]);
    }

    private function getLoginError(Request $request) {
        // get the error if any (works with forward and redirect -- see below)
        $session = $request->getSession();
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(Security::AUTHENTICATION_ERROR)) {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        } else {
            $error = null;
        }

        return $error;
    }

    private function save($obj) {
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($obj);
        $em->flush();
    }
}
