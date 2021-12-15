<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    8
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
namespace con4gis\OidcBundle\ContaoManager;

use con4gis\OidcBundle\con4gisOidcBundle;
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
        $loader->load('@con4gisOidcBundle/Resources/config/knpu_oauth2_client.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
    {
        return $resolver
            ->resolve(__DIR__.'/../Resources/config/routing.yml')
            ->load(__DIR__.'/../Resources/config/routing.yml')
            ;
    }

    /**
     * Gets a list of autoload configurations for this bundle.
     *
     * @param ParserInterface $parser
     *
     * @return ConfigInterface[]
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(KnpUOAuth2ClientBundle::class),
            BundleConfig::create(con4gisOidcBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class, KnpUOAuth2ClientBundle::class])
        ];
    }

    /**
     * Allows a plugin to override extension configuration.
     *
     * @param string           $extensionName
     * @param array            $extensionConfigs
     * @param ContainerBuilder $container
     *
     * @return array
     */
    public function getExtensionConfig($extensionName, array $extensionConfigs, ContainerBuilder $container)
    {
        if ('security' !== $extensionName) {
            return $extensionConfigs;
        }

        foreach ($extensionConfigs as &$extensionConfig) {
            if (isset($extensionConfig['firewalls'])) {

                // Add e.g. your own security authentication provider
                $extensionConfig['providers']['oauth'] = [
                    'id' => 'knpu.oauth2.user_provider'
                ];
                // Add your own firewall before the "frontend" firewall of Contao
                // Int-Cast position so "false" (not found) results in position 0.
                $offset = (int) array_search('frontend', array_keys($extensionConfig['firewalls']));

                $extensionConfig['access_control'][3]['roles'] = '%env(SECURED_FRONTEND)%';

                $accessControlOauth = [
                    'path' => '^/oauth',
                    'roles' => 'IS_AUTHENTICATED_ANONYMOUSLY'
                ];
                array_unshift($extensionConfig['access_control'], $accessControlOauth);
//                var_dump($extensionConfig);

                $extensionConfig['firewalls'] = array_merge(
                    array_slice($extensionConfig['firewalls'], 0, $offset, true),
                    [
                        'secured_frontend' => [
                            'pattern' => '^/(?!oauth/login|contao)',
                            'anonymous' => false,
                            'provider' => 'oauth',
                            'guard' => [
                                'authenticators' => [
                                    'oidc_authenticator'
                                ],
                            ],
                        ],
                    ],
                    array_slice($extensionConfig['firewalls'], $offset, null, true)
                );

                break;
            }
        }

        return $extensionConfigs;
    }

}