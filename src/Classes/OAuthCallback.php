<?php

namespace ronnybremer\OAuthBundle\Classes;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\Database;
use Contao\DataContainer;
use Contao\MemberModel;
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
            $currentRecord = MemberModel::findById($dc->id);
            if ($currentRecord == null) {
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
        $readOnlyFields = [1=>'username'];

        $allDCAFields = array_keys($GLOBALS['TL_DCA'][$dc->table]['fields']);

        foreach ($allDCAFields as $field) {
            if (array_search($field, $readOnlyFields)) {
                if (!array_key_exists('eval', $GLOBALS['TL_DCA'][$dc->table]['fields'][$field])) {
                    $GLOBALS['TL_DCA'][$dc->table]['fields'][$field]['eval'] = [];
                }

                $GLOBALS['TL_DCA'][$dc->table]['fields'][$field]['eval']['readonly'] = true;
            }
        }
    }

    public function oauthMemberMappingOptions(\MultiColumnWizard $dc = null) {
        $db = Database::getInstance();
        $memberFields = $db->getFieldNames("tl_member");
        $returnArray = ['-'];
        $skippedFields = ['id','tstamp','username','currentLogin','lastLogin','backupCodes','useTwoFactor','groups','login','assignDir','homeDir','disable','start','stop','dateAdded','session','locked','secret','backupCodes','trustedTokenVersion','newsletter','loginAttempts'];
        foreach ($memberFields as $memberField) {
            if (!in_array($memberField, $skippedFields)) {
                $returnArray[$memberField] = $memberField;
            }
        }
        return $returnArray;
    }
}
