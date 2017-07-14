<?php

class SPODSHOWCASE_CTRL_Admin extends ADMIN_CTRL_Abstract
{
    public function settings($params)
    {
        $this->setPageTitle("SHOWCASE");
        $this->setPageHeading("SHOWCASE");

        $form = new Form('settings');
        $this->addForm($form);

        $fb_app_id = new TextField('app_id');
        $preference = BOL_PreferenceService::getInstance()->findPreference('fb_app_id');
        $fb_app_id_pref = empty($preference) ? "0000000" : $preference->defaultValue;
        $fb_app_id->setValue($fb_app_id_pref);
        $form->addElement($fb_app_id);

        $submit = new Submit('add');
        $submit->setValue('SAVE');
        $form->addElement($submit);

        if ( OW::getRequest()->isPost() && $form->isValid($_POST))
        {
            $data = $form->getValues();

            $preference = BOL_PreferenceService::getInstance()->findPreference('fb_app_id');

            if(empty($preference))
                $preference = new BOL_Preference();

            $preference->key = 'fb_app_id';
            $preference->sectionName = 'general';
            $preference->defaultValue = $data['app_id'];
            $preference->sortOrder = 1;
            BOL_PreferenceService::getInstance()->savePreference($preference);
        }
    }
}