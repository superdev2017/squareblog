<?php

namespace Shop\UserBundle\Security\Authenticator;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimpleFormAuthenticatorInterface;

class UserAuthenticator implements SimpleFormAuthenticatorInterface
{
    use ContainerAwareTrait;

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $translator = $this->container->get('translator');

        try {
            $user = $userProvider->loadUserByUsername($token->getUsername());
        } catch (UsernameNotFoundException $e) {
            // CAUTION: this message will be returned to the client
            // (so don't put any un-trusted messages / error strings here)
            throw new CustomUserMessageAuthenticationException($translator->trans('Invalid username or password.'));
        }

        $passwordValid = $this->encoder->isPasswordValid($user, $token->getCredentials());

        // If not admin, and no active subscription, do not allow login
        if ($passwordValid) {
            if (!$user->hasRole('ROLE_ADMIN')) {
                $um = $this->container->get('user_manager');
                $subscription = $um->getSubscription($user);

                if ($subscription->status == 'canceled') {
                    throw new CustomUserMessageAuthenticationException($translator->trans('Your subscription has been canceled.'));
                }
            }

            return new UsernamePasswordToken(
                $user,
                $user->getPassword(),
                $providerKey,
                $user->getRoles()
            );
        }

        throw new CustomUserMessageAuthenticationException($translator->trans('Invalid username or password.'));
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordToken
        && $token->getProviderKey() === $providerKey;
    }

    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }

    private function save($obj)
    {
        $this->container->get('doctrine')->getEntityManager()->persist($obj);
        $this->container->get('doctrine')->getEntityManager()->flush();
    }
}