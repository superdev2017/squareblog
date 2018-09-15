<?php

namespace Shop\UserBundle\Admin;

use Shop\UserBundle\Enum\UserRoleEnum;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class UserAdmin extends AbstractAdmin
{
    use ContainerAwareTrait;

    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {

        $supportedCountries = $this->container->getParameter('supported_countries');

        $countries = [];
        foreach ($supportedCountries as $supportedCountry) {
            $countries[$supportedCountry['country_code']] = $supportedCountry['country_code'];
        }

        $formMapper
            ->with('General')
            ->add('username')
            ->add('email')
            ->add('plainPassword', 'text', array('required' => false))
            ->end()
            ->with('Management')
            ->add(
                'role',
                'choice',
                [
                    'choices' => array_flip(UserRoleEnum::toArray()),
                ]
            )
            ->add('countryCode',
                'choice', [
                    'choices' => $countries
                ]
            )
            ->add(
                'api_key',
                'text',
                [
                    'label' => 'The API key is auto generated if the user has ROLE_API_{ANY}',
                    'disabled' => true,
                ]
            )
            ->add('enabled', null, array('required' => false))
            ->end();
    }

    public function prePersist($user)
    {
        if (!$user->getApiKey()) {
            $user->generateApiKey();
        }
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('username')
            ->add('email');
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('username')
            ->add('email')
            ->add('role')
            ->add('enabled');
    }
}