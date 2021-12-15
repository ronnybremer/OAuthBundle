<?php

namespace con4gis\OidcBundle\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class KeycloakController extends AbstractController
{
    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/oauth/login", name="connect_oidc_start")
     */
    public function connectAction()
    {
        $clientRegistry = $this->get('knpu.oauth2.registry');
        // will redirect to sso!
        return $clientRegistry
            ->getClient('oidc') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect();
    }

    /**
     * After going to SSO, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/oauth/callback", name="connect_oidc_check")
     */
    public function connectCheckAction(Request $request) { }
}