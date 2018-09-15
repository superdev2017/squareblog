<?php
/**
 * Created by PhpStorm.
 * User: ivanristic
 * Date: 12/31/15
 * Time: 10:48 AM
 */

namespace App\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Shop\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * Site
 *
 * @ORM\Table()
 * @ORM\Entity()
 * @ORM\MappedSuperclass
 * @ExclusionPolicy("all")
 */
class Site
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="text", length=250)
     * @Expose
     */
    private $name;

    /**
     * @ORM\Column(name="title", type="text", length=250, nullable=true)
     * @Expose
     */
    private $title;

    /**
     * @ORM\Column(name="description", type="text", length=2500, nullable=true)
     * @Expose
     */
    private $description;

    /**
     * @ORM\Column(name="slug", type="text", length=250, nullable=true)
     * @Expose
     */
    private $slug;

    /**
     * @ORM\Column(name="template", type="text", length=250)
     */
    private $template  = 'Blogalicious';
    /**
     * @ORM\ManyToOne(targetEntity="Shop\UserBundle\Entity\User", inversedBy="sites", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    protected $user;

    /**
     * @ORM\Column(name="content", type="text", length=500000, nullable=true)
     */
    private $content;


    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param $slug
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param $user
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function __toString() {
        return $this->getName();
    }
}