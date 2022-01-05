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
 
use Contao\MemberModel;

class LoginUserHandler
{

    public function addUser ($userArray) {

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
        $feUser->email = $userArray['email'];
        $feUser->firstname = $userArray['firstname'];
        $feUser->lastname = $userArray['lastname'];
        $feUser->username = $userArray['username'];
        $feUser->save();

        return $feUser;
    }
}