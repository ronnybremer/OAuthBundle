<?php

$cbClass = ronnybremer\OAuthBundle\Classes\OAuthCallback::class;

$GLOBALS['TL_DCA']['tl_module']['palettes']['oauth_login'] = '{title_legend},name,type,c4g_oauth_type,c4g_oauth_btn_name,c4g_oauth_reg_groups,c4g_oauth_member_mapping;';

$GLOBALS['TL_DCA']['tl_module']['fields']['oauth_type'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['oauth']['fields']['type'],
    'exclude'                 => true,
    'filter'                  => false,
    'inputType'               => 'select',
    'options'                 => [
        '/oidc/login'               => &$GLOBALS['TL_LANG']['tl_module']['oauth']['fields']['type_oidc'],
    ],
    'default'                 => 'oidc',
    'eval'                    => ['submitOnChange' => false, 'mandatory' => true, 'tl_class' => 'clr'],
    'sql'                     => "varchar(32) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_module']['fields']['oauth_btn_name'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['oauth']['fields']['btn_name'],
    'exclude'                 => true,
    'filter'                  => false,
    'inputType'               => 'text',
    'default'                 => 'Login',
    'eval'                    => ['submitOnChange' => false, 'mandatory' => true],
    'sql'                     => "varchar(256) NOT NULL default ''",
);
