<?php
// не используется вданный момент
class Module_Index_Controller_BackendInto extends Module_Common_Controller_Common
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            return $this->createNotification()
                        ->setMessage('forbidden_access')
                        ->setType('alert')
                        ->setRedirectUrl(Base_Redirect::implode('admin'))
                        ->run();
        }

        $this->getView()->loadI18n('Common/BackendGeneral', $this->getVirtualControllerPath());
        $this->getView()->getHelper('Html_Title')->add($this->getView()->lang['title']);

        return $this->getView();
    }
}