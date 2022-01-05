<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   east_frisia
 * @author    contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */

$GLOBALS['TL_DCA']['tl_module']['palettes']['oauth_login'] = '{title_legend},name,type,c4g_oauth_type,c4g_oauth_btn_name;';


$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_oauth_type'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_oauth']['fields']['type'],
    'exclude'                 => true,
    'filter'                  => false,
    'inputType'               => 'select',
    'options'                 => [
        '/oidc/login'               => &$GLOBALS['TL_LANG']['tl_module']['c4g_oauth']['fields']['type_oidc'],
    ],
    'default'                 => 'oidc',
    'eval'                    => ['submitOnChange' => false, 'tl_class' => 'w50', 'mandatory' => true],
    'sql'                     => "varchar(32) unsigned NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_oauth_btn_name'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_oauth']['fields']['btn_name'],
    'exclude'                 => true,
    'filter'                  => false,
    'inputType'               => 'text',
    'default'                 => 'Login',
    'eval'                    => ['submitOnChange' => false, 'tl_class' => 'w50', 'mandatory' => true],
    'sql'                     => "varchar(256) unsigned NOT NULL default ''",
);