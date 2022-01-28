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
namespace con4gis\OAuthBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use con4gis\OAuthBundle\DependencyInjection\con4gisOAuthExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class con4gisOAuthBundle
 * @package con4gis\OAuthBundle
 */
class con4gisOAuthBundle extends Bundle
{
    public function getContainerExtension(): con4gisOAuthExtension
    {
//         Set alias con4gis_oauth
        return new con4gisOAuthExtension();
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }
}