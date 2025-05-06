<?php

namespace ronnybremer\OAuthBundle\Resources\contao\modules;

use ronnybremer\CoreBundle\Resources\contao\models\LogModel;
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
            \System::loadLanguageFile('fe_oauth_login');
            $template = $this->Template;
        } catch(\Throwable $e) {
            LogModel::addLogEntry('oauth', $e->getMessage());
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
