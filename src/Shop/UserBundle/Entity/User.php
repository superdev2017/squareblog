<?php

namespace Shop\UserBundle\Entity;

use App\SiteBundle\Entity\Site;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Shop\UserBundle\Enum\UserRole;
use Shop\UserBundle\Enum\UserRoleEnum;
use Uecode\Bundle\ApiKeyBundle\Entity\ApiKeyUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="shop_user")
 */
class User extends ApiKeyUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $subscriptionId;

    /**
     * @ORM\OneToMany(targetEntity="App\SiteBundle\Entity\Site", mappedBy="user", cascade={"persist"})
     */
    protected $sites;

    /**
     * @ORM\Column(name="country_code", type="string", length=255, options={"default": "DK"})
     */
    protected $countryCode = 'DK';

    /**
     * @var boolean $intro
     * @ORM\Column(name="intro", type="boolean")
     */
    private $intro = false;

    /**
     * @ORM\Column(name="one_time_login_token", type="string", length=1000, nullable=true)
     */
    protected $oneTimeLoginToken;

    private $roleWrapper;

    public function __construct()
    {
        $this->sites = new ArrayCollection();
        parent::__construct();
    }

    public function generateApiKey()
    {
        $random = random_bytes(50);
        $this->setApiKey(md5($random));
    }

    /**
     * @return int
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }

    /**
     * @param $subscriptionId
     * @return $this
     */
    public function setSubscriptionId($subscriptionId)
    {
        $this->subscriptionId = $subscriptionId;
        return $this;
    }


    public function getRoleWrapper()
    {
        if (!isset($this->roleWrapper)) {
            $this->roleWrapper = new UserRole($this->getRole());
        }

        return $this->roleWrapper;
    }

    /**
     * Returns the user roles
     *
     * @return array The roles
    */
    public function getRoles()
    {
        $roles = $this->roles;

        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        // we need to make sure to have at least one role
        $roles[] = UserRoleEnum::ROLE_CUSTOMER;

        return array_unique($roles);
    }

    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === UserRoleEnum::ROLE_USER) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function setRole($role)
    {
        $this->setRoles([$role]);
    }

    public function getRole() {
        $role = $this->getRoles()[0];
        return $role;
    }

    public function hasRole($role = null)
    {
        if (!$role)
            return false;

        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * @return mixed
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param $countryCode
     * @return $this
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSites()
    {
        return $this->sites;
    }

    /**
     * @param $sites
     * @return $this
     */
    public function setSites($sites)
    {
        $this->sites = $sites;
        return $this;
    }

    /**
     * @param Site $site
     */
    public function addSite(Site $site)
    {
        if ($this->sites->contains($site)) {
            return;
        }
        $site->setUser($this);

        $this->sites->add($site);
    }

    /**
     * @param Site $site
     */
    public function removeSite(Site $site)
    {
        if (!$this->sites->contains($site)) {
            return;
        }
        $site->setUser(null);

        $this->sites->removeElement($site);
    }

    /**
     * @return mixed
     */
    public function getIntro()
    {
        return $this->intro;
    }

    /**
     * @param $intro
     * @return $this
     */
    public function setIntro($intro)
    {
        $this->intro = $intro;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOneTimeLoginToken()
    {
        return $this->oneTimeLoginToken;
    }

    public function setOneTimeLoginToken($oneTimeLoginToken)
    {
        $this->oneTimeLoginToken = $oneTimeLoginToken;
        return $this;
    }

    public function generateOneTimeLoginToken()
    {
        $random = random_bytes(50);
        $this->oneTimeLoginToken = md5($random);
    }

    public function __toString()
    {
        return (string)$this->getUsername();
    }
}
