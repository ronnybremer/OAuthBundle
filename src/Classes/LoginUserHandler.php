<?php
/**
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package   	con4gis
 * @version        8
 * @author  	    con4gis contributors (see "authors.txt")
 * @license 	    LGPL-3.0-or-later
 * @copyright 	Küstenschmiede GmbH Software & Design
 * @link              https://www.con4gis.org
 *
 */

namespace con4gis\OidcBundle\Classes;
 
use con4gis\OidcBundle\Resources\contao\models\OAuthMemberModel;
use Contao\Database;

class LoginUserHandler
{

    private $db = null;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function addUser ($userArray, $loginRoute) {

        $feUser = OAuthMemberModel::findByUsername($userArray['username']);
        if (!$feUser) {
            $feUser = new OAuthMemberModel();
            $feUser->dateAdded = time();
        } else {
            $feUser->lastLogin = $feUser->currentLogin;
        }
        $feUser->c4gOAuthMember = 1;
        $feUser->login = 1;
        $feUser->tstamp = time();
        $feUser->currentLogin = time();
        $feUser->email = $userArray['email'];
        $feUser->firstname = $userArray['firstname'];
        $feUser->lastname = $userArray['lastname'];
        $feUser->username = $userArray['username'];

        //add user to groups
        $loginModule = $this->db->prepare("SELECT * FROM tl_module WHERE c4g_oauth_type = ?")
            ->execute($loginRoute)->fetchAssoc();
        $feUserGroups = $feUser->groups;
        if ($feUserGroups && $feUserGroups != 'a:0:{}') {
            $feUserGroups = unserialize($feUserGroups);
            $loginGroups = unserialize($loginModule['c4g_oauth_reg_groups']);
            $missingGroups = array_diff($loginGroups, $feUserGroups);
            if ($missingGroups) {
                foreach ($missingGroups as $missingGroup) {
                    $feUserGroups[] = $missingGroup;
                }
                $feUser->groups = serialize($feUserGroups);
            }
        } else {
            $feUser->groups = $loginModule['c4g_oauth_reg_groups'];
        }

        //save member
        $feUser->save();

        return $feUser;
    }
}