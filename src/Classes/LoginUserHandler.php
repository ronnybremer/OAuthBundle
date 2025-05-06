<?php

namespace ronnybremer\OAuthBundle\Classes;

use Contao\Database;
use Contao\MemberModel;

class LoginUserHandler
{

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function addUser ($userArray, $loginRoute) {

        $feUser = MemberModel::findByUsername($userArray['username']);
        if (!$feUser) {
            $feUser = new MemberModel();
            $feUser->dateAdded = time();
        } else {
            $feUser->lastLogin = $feUser->currentLogin;
        }
        $feUser->login = 1;
        $feUser->tstamp = time();
        $feUser->currentLogin = time();
        $feUser->username = $userArray['username'];

        //map additional member information
        $memberMappings = $loginModule['oauth_member_mapping'];
        if ($memberMappings && $memberMappings != 'a:0:{}') {
            $memberMappings = unserialize($memberMappings);
            foreach ($memberMappings as $memberMapping) {
                if ($memberMapping['contaoField'] == '0') {
                    break;
                }
                $contaoField = $memberMapping['contaoField'];
                $oauthField = $memberMapping['oauthField'];
                $feUser->$contaoField = $userArray[$oauthField];
            }
        }

        //save member
        $feUser->save();

        return $feUser;
    }
}
