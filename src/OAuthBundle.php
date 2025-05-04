<?php
namespace ronnybremer\OAuthBundle;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use ronnybremer\OAuthBundle\DependencyInjection\OAuthExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OAuthBundle extends AbstractBundle
{
    public function loadExtension(
        array $config,
        ContainerConfigurator $containerConfigurator,
        ContainerBuilder $containerBuilder,
    ): void
    {
        $containerConfigurator->import('../config/services.yaml');
    }
}
