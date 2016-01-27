<?php

namespace HMLB\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use RuntimeException;

/**
 * SecurityController.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class SecurityController extends Controller
{
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            $this->container->getParameter('hmlb_user.login.template'),
            [
                'last_username' => $lastUsername,
                'error' => $error,
            ]
        );
    }

    public function loginCheckAction()
    {
        throw new RuntimeException(
            'You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.'
        );
    }

    public function logoutAction()
    {
        throw new RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}
