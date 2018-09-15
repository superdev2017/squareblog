<?php
/**
 * Created by PhpStorm.
 * User: ivanristic
 * Date: 12/20/16
 * Time: 1:53 PM
 */

namespace App\NotificationBundle\Entity;

use App\NotificationBundle\Enum\NotificationStatusEnum;
use Kf\KitBundle\Doctrine\ORM\Traits as KFT;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class Notification
{
    use KFT\Entity,
        KFT\StatusableEntity,
        KFT\TimestampableEntity,
        KFT\SoftDeleteableEntity;

    /**
     * @ORM\Column(name="type", type="string", length=255)
     */
    protected $type;

    /**
     * @ORM\Column(name="template", type="string", length=255, nullable=true)
     */
    protected $template;

    /**
     * @ORM\Column(name="parameters", type="json_array", length=255, nullable=true)
     */
    protected $parameters;

    /**
     * @ORM\Column(name="priority", type="integer")
     */
    protected $priority;

    /**
     * @ORM\ManyToOne(targetEntity="\shop.user.class", inversedBy="notifications", cascade={"persist"})
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    protected $customer;

    public function __construct($type, $template, $parameters, $priority = 1, $status = NotificationStatusEnum::PENDING)
    {
        $this->type = $type;
        $this->template = $template;
        $this->parameters = $parameters;
        $this->priority = $priority;
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
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
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param $parameters
     * @return $this
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param $priority
     * @return $this
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param $customer
     * @return $this
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
        return $this;
    }
}