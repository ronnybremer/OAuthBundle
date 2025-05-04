<?php
namespace con4gis\OAuthBundle\ContaoManager;

use ronnybremer\OAuthBundle\OAuthBundle;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Config\ConfigInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Config\ConfigPluginInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Contao\ManagerPlugin\Config\ContainerBuilder;
use Contao\ManagerPlugin\Config\ExtensionPluginInterface;
use Contao\ManagerPlugin\Dependency\DependentPluginInterface;
use KnpU\OAuth2ClientBundle\KnpUOAuth2ClientBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\KernelInterface;

class Plugin implements BundlePluginInterface, ExtensionPluginInterface, RoutingPluginInterface, ConfigPluginInterface
{
    public function registerContainerConfiguration(LoaderInterface $loader, array $config)
    {
        $loader->load('@OAuthBundle/Resources/config/config.yml');
        $loader->load('@OAuthBundle/Resources/config/knpu_oauth2_client.yml');
        $loader->load(__DIR__.'/../Resources/config/services.yml');
    }

    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
    {
        return $resolver
            ->resolve(__DIR__.'/../Resources/config/routing.yml')
            ->load(__DIR__.'/../Resources/config/routing.yml')
            ;
    }

    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(KnpUOAuth2ClientBundle::class),
            BundleConfig::create(OAuthBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class, KnpUOAuth2ClientBundle::class])
        ];
    }

    public function getExtensionConfig($extensionName, array $extensionConfigs, ContainerBuilder $container)
    {
        if ('security' !== $extensionName) {
            return $extensionConfigs;
        }

        foreach ($extensionConfigs as &$extensionConfig) {
            if (isset($extensionConfig['firewalls'])) {

                //Add custom encoder for knpu oauth2
                $extensionConfig['encoders']['KnpU\OAuth2ClientBundle\Security\User\OAuthUser'] = [
                    'algorithm' => 'auto'
                ];

                // Add own security authentication provider
                $extensionConfig['providers']['oauth'] = [
                    'id' => 'knpu.oauth2.user_provider'
                ];

                $extensionConfig['providers']['frontend_chain'] = [
                    'chain' => [
                        'providers' => ['oauth','contao.security.frontend_user_provider']
                    ]
                ];

                $offset = (int) array_search('frontend', array_keys($extensionConfig['firewalls']));

                $extensionConfig['firewalls']['contao_frontend']['guard'] = [
                    'authenticators' => [
                        'oidc_authenticator'
                    ]
                ];
                unset($extensionConfig['firewalls']['contao_frontend']['request_matcher']);
                if ($container->getParameter('oauth.oidc.secured') == 'true') {
                    $extensionConfig['access_control'][3]['roles'] = "ROLE_OAUTH_USER";

                    $extensionConfig['firewalls']['contao_frontend']['entry_point'] = 'oidc_authenticator';
                    $extensionConfig['firewalls']['contao_frontend']['pattern'] = '^/(?!oidc/login|oidc/login|contao)';
                    $extensionConfig['firewalls']['contao_frontend']['provider'] = 'frontend_chain';
                    $extensionConfig['firewalls']['contao_frontend']['anonymous'] = false;
                }

                break;
            }
        }

        return $extensionConfigs;
    }

}
