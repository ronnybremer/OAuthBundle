<?php

use ronnybremer\OAuthBundle\Classes\OAuthCallback;

$GLOBALS['TL_DCA']['tl_member']['config']['onload_callback'][] = [
    OAuthCallback::class, 'onLoadCallback'
];

