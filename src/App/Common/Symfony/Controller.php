<?php

namespace App\Common\Symfony;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use App\Common\Symfony\Controller\ControllerTrait;

class Controller extends BaseController
{
    use ControllerTrait;

}