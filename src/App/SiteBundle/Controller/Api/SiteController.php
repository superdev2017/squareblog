<?php

namespace App\SiteBundle\Controller\Api;

use App\SiteBundle\Entity\Site;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as FW;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Common\Symfony\Controller\RestController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SiteController extends FOSRestController
{

    /**
     * Used by Shop to create a user
     *
     * @Rest\View
     * @Rest\Post("/upload/image", options={"expose"=true})
     * @FW\Security("has_role('ROLE_USER')")
     */
    public function uploadImageAction(Request $request) {

        \Cloudinary::config(array(
            "cloud_name" => $this->getParameter('cloud_name'),
            "api_key" => $this->getParameter('cloud_apikey'),
            "api_secret" => $this->getParameter('cloud_secret')
        ));

        $file = $request->files->get('image');

        if (!$file)
            throw new BadRequestHttpException('No file provided');

        $response = \Cloudinary\Uploader::upload($file);

        return ['status' => 'completed', 'data' => $response];
    }

    /**
     * Used by Shop to create a user
     *
     * @Rest\View
     * @Rest\Post("/site/save", options={"expose"=true})
     * @FW\Security("has_role('ROLE_USER')")
     */
    public function siteSaveAction(Request $request) {

        $html = $request->request->get('html');
        $id = $request->request->get('id');

        if (!$id)
            return [['status' => 'error']];


        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $html);

        $site = $this->getUserSite($id);
        if (!$site)
            return [['status' => 'error']];

        $site->setContent($html)
            ->setUser($this->getUser());

        $em = $this->getDoctrine()->getEntityManager();

        $em->persist($site);
        $em->flush();

        return ['status' => 'completed'];
    }

    /**
     * @Rest\View
     * @Rest\Post("/site/create", options={"expose"=true})
     * @FW\Security("has_role('ROLE_USER')")
     */
    public function createSiteAction(Request $request) {
        $name = $request->request->get('name');
        $slug = strtolower($request->request->get('slug'));
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $template = $request->request->get('template');

        if (empty($name) || empty($template))
            return [['status' => 'error', 'message' => 'Name and Template are mandatory.']];

        // Check if user already has site with same slug
        if ($this->getUserSiteFromSlug($slug) !== null) {
            return [['status' => 'error', 'message' => 'You cannot have 2 sites with the same slug.']];
        }

        $template = 'Blogalicious';

        $site = new Site();
        $site->setName($name)
            ->setSlug($slug)
            ->setTitle($title)
            ->setDescription($description)
            ->setTemplate($template)
            ->setUser($this->getUser());

        $em = $this->getDoctrine()->getEntityManager();

        $em->persist($site);
        $em->flush();

        $url = $this->get('router')->generate(
            'light_builder_edit',
            [
                'id'  => $site->getId()
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return ['status' => 'completed', 'url' => $url];
    }


    /**
     * @Rest\View
     * @Rest\Post("/site/delete", options={"expose"=true})
     * @FW\Security("has_role('ROLE_USER')")
     */
    public function deleteSiteAction(Request $request) {
        $id = $request->request->get('id');

        if (empty($id) || !is_numeric($id))
            return [['status' => 'error', 'message' => 'Please try again.']];

        $toDelete = $this->getUserSite($id);

        if (!$toDelete)
            return [['status' => 'error', 'message' => 'Cannot delete site that does not belong to you.']];

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($toDelete);
        $em->flush();


        return ['status' => 'completed'];
    }


    /**
     * @Rest\View
     * @Rest\Post("/site/config/update", options={"expose"=true})
     * @FW\Security("has_role('ROLE_USER')")
     */
    public function updateSiteConfigAction(Request $request) {
        $id = $request->request->get('id');
        $name = $request->request->get('name');
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $slug = strtolower($request->request->get('slug'));

        if (empty($id) || empty($name))
            return [['status' => 'error', 'message' => 'A site name is required.']];

        $site = $this->getUserSite($id);
        if (!$site)
            return [['status' => 'error', 'message' => 'Cannot update site that does not belong to you.']];

        if ($this->getUserSiteFromSlug($slug) !== null) {
            return [['status' => 'error', 'message' => 'You cannot have 2 sites with the same slug.']];
        }

        $site->setName($name)
            ->setTitle($title)
            ->setDescription($description)
            ->setSlug($slug)
            ->setUser($this->getUser());

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($site);
        $em->flush();

        return ['status' => 'completed'];
    }

    /**
     * @Rest\View
     * @Rest\Post("/site/config/get", options={"expose"=true})
     * @FW\Security("has_role('ROLE_USER')")
     */
    public function getSiteConfigAction(Request $request) {
        $id = $request->request->get('id');
        if (empty($id) || !is_numeric($id))
            return [['status' => 'error', 'message' => 'Please try again.']];

        $site = $this->getUserSite($id);
        if (empty($site))
            return [['status' => 'error', 'message' => 'Please try again.']];

        return ['status' => 'completed', 'site' => $site];
    }

    private function getUserSite($id) {
        $sites = $this->getUser()->getSites();
        $chosenSite = null;
        foreach ($sites as $site) {
            if ($site->getId() == $id) {
                return $site;
            }
        }
        return null;
    }

    private function getUserSiteFromSlug($slug) {
        $sites = $this->getUser()->getSites();
        $chosenSite = null;
        foreach ($sites as $site) {
            if ($site->getSlug() == $slug) {
                return $site;
            }
        }
        return null;
    }
}