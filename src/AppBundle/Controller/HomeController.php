<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class HomeController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @Template
     */
    public function indexAction(Request $request)
    {
    }
}
