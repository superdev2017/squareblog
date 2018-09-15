<?php

namespace App\Common\Doctrine\ORM\Traits;

trait PriceableEntity{

    /**
     * @\Doctrine\ORM\Mapping\Column(type="decimal", precision=7, scale=2)
     */
    protected $price;

    /**
     * @\Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    protected $currency;

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }
}