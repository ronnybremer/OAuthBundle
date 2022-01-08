<?php

namespace con4gis\OidcBundle\Security;

use Contao\System;
use Contao\CoreBundle\Framework\ContaoFramework;
use con4gis\OAuthBundle\Classes\LoginUserHandler;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class OidcAuthenticator extends SocialAuthenticator
{
    use TargetPathTrait;

    private $clientRegistry;
    private $em;
    private $router;
    private $framework;

    public function __construct(ContaoFramework $contaoFramework, ClientRegistry $clientRegistry, RouterInterface $router, EntityManagerInterface $em)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
        $this->framework = $contaoFramework;
    }

    public function supports(Request $request)
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_oidc_check';
    }

    public function getCredentials(Request $request)
    {
        // this method is only called if supports() returns true
        return $this->fetchAccessToken($this->getOidcClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if ($_ENV['SECURED'] == 'true') {
            $user = $userProvider->loadUserByUsername($this->getOidcClient()->fetchUserFromToken($credentials)->getId());
        } else {
            $oidcUser = $this->getOidcClient()->fetchUserFromToken($credentials);
            $oidcUser = $oidcUser->toArray();

            $firstname = $oidcUser['given_name'];
            $lastname = $oidcUser['family_name'];
            $username = $oidcUser['preferred_username'];
            $email = $oidcUser['email'];

            $userArray = [
                'username' => $username,
                'email' => $email,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'groups' => []
            ];

            $this->framework->initialize();
            $loginUser = new LoginUserHandler();
            $feUser = $loginUser->addUser($userArray, '/oidc/login');

            $user = $userProvider->loadUserByUsername($feUser->username);
        }

        return $user;
    }

    /**
     * @return OAuth2ClientInterface
     */
    private function getOidcClient()
    {
        return $this->clientRegistry
            // "oidc" is the key used in config/packages/knpu_oauth2_client.yaml
            ->getClient('oidc');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath, Response::HTTP_TEMPORARY_REDIRECT);
        }

        return new RedirectResponse('/',
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/oidc/login',
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}