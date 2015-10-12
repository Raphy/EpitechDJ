<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class AuthenticationController extends Controller
{
    /**
     * @Template
     */
    public function loginAction(Request $request)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('app_index'));
        }
        $session = $request->getSession();
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        }
        return array(
            'last_username' => $session->get(Security::LAST_USERNAME),
            'error' => $error,
        );
    }
}
