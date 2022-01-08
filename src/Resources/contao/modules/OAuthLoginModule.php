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
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2019
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\OAuthBundle\Resources\contao\modules;

use con4gis\CoreBundle\Resources\contao\models\C4gLogModel;
use Contao\FrontendUser;

class OAuthLoginModule extends \Module
{
    protected $strTemplate = 'oauth_login_template';

    /**
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### ' . $this->name . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        if (!$_POST && $this->redirectBack && ($strReferer = $this->getReferer()) != \Environment::get('request'))
        {
            $_SESSION['LAST_PAGE_VISITED'] = $strReferer;
        }

        return parent::generate();
    }

    protected function compile()
    {
        try {
            \System::loadLanguageFile('fe_c4g_oauth_login');
            $template = $this->Template;
        } catch(\Throwable $e) {
            C4gLogModel::addLogEntry('oauth', $e->getMessage());
            return;
        }
        // Get user id when user is logged in
        if (FE_USER_LOGGED_IN === true) {
            $objUser = FrontendUser::getInstance();
            $userId = $objUser->id;
            $template->loginStatus = $userId;
        } else {
            $template->loginStatus = false;
        }
    }
}