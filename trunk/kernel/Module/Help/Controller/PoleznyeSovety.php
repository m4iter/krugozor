<?php
class Module_Help_Controller_PoleznyeSovety extends Module_Common_Controller_Common
{
    public function run()
    {
        parent::common();

        $this->getView()->loadI18n('Common/FrontendGeneral');

        $this->getView()->getHelper('Html_Title')->add($this->getView()->lang['title']);

        $this->getView()->current_user = $this->getCurrentUser();

        return $this->getView();
    }
}