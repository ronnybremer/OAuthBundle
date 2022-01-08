<?php
/**
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package     con4gis
 * @version     8
 * @author      con4gis contributors (see "authors.txt")
 * @license     LGPL-3.0-or-later
 * @copyright   Küstenschmiede GmbH Software & Design
 * @link        https://www.con4gis.org
 */
namespace con4gis\OAuthBundle\Classes;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\DataContainer;
use con4gis\OAuthBundle\Resources\contao\models\OAuthMemberModel;
use Contao\Message;

class OAuthCallback
{
    /**
     * Remove password field, make OAuth data read-only if OAuth Member
     * @param DataContainer $dc
     */
    public function onLoadCallback(DataContainer $dc = null) : void
    {
        if ($dc->id == null) {
            return;
        }

        $currentRecord = null;

        if ($dc->table == 'tl_member') {
            $currentRecord = OAuthMemberModel::findById($dc->id);
            if ($currentRecord == null || $currentRecord->c4gOAuthMember == 0) {
                return;
            }
        }

        //remove password field
        if ($dc->table == 'tl_member') {
            PaletteManipulator::create()
                ->removeField('password')
                ->applyToSubpalette('login', 'tl_member');
        }

        //Set default fields to read only
        $readOnlyFields = [1=>'firstname',2=>'lastname',3=>'email',4=>'username'];

        $allDCAFields = array_keys($GLOBALS['TL_DCA'][$dc->table]['fields']);

        foreach ($allDCAFields as $field) {
            if (array_search($field, $readOnlyFields)) {
                if (!array_key_exists('eval', $GLOBALS['TL_DCA'][$dc->table]['fields'][$field])) {
                    $GLOBALS['TL_DCA'][$dc->table]['fields'][$field]['eval'] = [];
                }

                $GLOBALS['TL_DCA'][$dc->table]['fields'][$field]['eval']['readonly'] = true;
            }
        }

        Message::addInfo($GLOBALS['TL_LANG'][$dc->table]['c4g_oauth_readonly_info']);
    }

    public function oauthMemberMappingOptions(\MultiColumnWizard $dc = null) {
        return [];
    }
}