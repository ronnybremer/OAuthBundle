<?php

array_insert($GLOBALS['FE_MOD']['oauth'], 1, array
(
    'oauth_login' => \ronnybremer\OAuthBundle\Resources\contao\modules\OAuthLoginModule::class,
));

if (is_array($GLOBALS['BE_MOD']) && isset($GLOBALS['BE_MOD']['oauth']) && $GLOBALS['BE_MOD']['oauth']) {
    $GLOBALS['BE_MOD']['oauth'] = array_merge($GLOBALS['BE_MOD']['oauth'], ['oauth'=>['brick' => 'oauth']]);
};
