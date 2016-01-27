<?php

namespace HMLB\UserBundle\Security;

use HMLB\UserBundle\User\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Http\RememberMe\PersistentTokenBasedRememberMeServices;
use Symfony\Component\Security\Http\RememberMe\TokenBasedRememberMeServices;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

/**
 * LoginManager.
 *
 * @author Hugues Maignol <hugues@hmlb.fr>
 */
class LoginManager
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var UserCheckerInterface
     */
    private $userChecker;

    /**
     * @var SessionAuthenticationStrategyInterface
     */
    private $sessionStrategy;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var TokenBasedRememberMeServices
     */
    private $tokenBasedRememberMeServices;

    /**
     * @var PersistentTokenBasedRememberMeServices
     */
    private $persistentRememberMeServices;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        UserCheckerInterface $userChecker,
        SessionAuthenticationStrategyInterface $sessionStrategy,
        RequestStack $requestStack,
        PersistentTokenBasedRememberMeServices $persistentRememberMeServices = null,
        TokenBasedRememberMeServices $tokenBasedRememberMeServices = null
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->userChecker = $userChecker;
        $this->sessionStrategy = $sessionStrategy;
        $this->requestStack = $requestStack;
        $this->tokenBasedRememberMeServices = $tokenBasedRememberMeServices;
        $this->persistentRememberMeServices = $persistentRememberMeServices;
    }

    final public function loginUser($firewallName, User $user, Response $response = null)
    {
        $this->userChecker->checkPostAuth($user);

        $token = $this->createToken($firewallName, $user);
        $request = $this->requestStack->getCurrentRequest();
        if ($request == $this->requestStack->getMasterRequest()) {
            $this->sessionStrategy->onAuthentication($request, $token);
        }
        //TODO: implement rememberme

        $this->tokenStorage->setToken($token);
    }

    protected function createToken($firewall, User $user)
    {
        return new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
    }
}
