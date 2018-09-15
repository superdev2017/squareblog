<?php
/**
 * Created by PhpStorm.
 * User: ivanristic
 * Date: 1/18/17
 * Time: 11:56 AM
 */

namespace Shop\ShopBundle\Controller\Front;

use Mailgun\Mailgun;
use Shop\ShopBundle\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ShopController extends Controller {
    /**
     * @Route("/get-in-touch", name="contact")
     */
    public function contactAction(Request $request)
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        if ($form->isValid()) {

            // Get twig globals
            $twig = $this->get('twig');
            $twigGlobals = $twig->getGlobals();

            // Get form data
            $formData = $form->getData();

            // Send mail
            $body = $this->get('templating')->render(
                'email/contact.html.twig',
                [
                    'data' => $formData
                ]
            );

            $mg = new Mailgun($this->getParameter('mailgun_privatekey'));
            $mg->sendMessage($this->getParameter('mailgun_domain'), [
                    'from'    => $formData['email'],
                    'to'      => $twigGlobals['support_email'],
                    'subject' => 'New request from: ' . $formData['email'],
                    'html'    => $body
                ]
            );

            $session = $request->getSession();
            $session->start();
            $session->getFlashBag()->add('success',
                "Thank you for contact us, we will try to respond within 24 hours."
            );
        }


        return $this->render('pages/contact.html.twig', [
            'contactForm' => $form->createView()
        ]);
    }
}