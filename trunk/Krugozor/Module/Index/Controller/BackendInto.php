<?php
// не используется вданный момент
class Module_Index_Controller_BackendInto extends Module_Common_Controller_Common
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            $redirect = new Base_Redirect($this->getDb());
            $redirect->setMessage('forbidden_access');
            $redirect->setType('alert');
            $redirect->setRedirectUrl(Base_Redirect::implode('admin'));
            return $redirect->run();
        }

        $this->getView()->loadI18n('Common/BackendGeneral', $this->getVirtualControllerPath());
        $this->getView()->getHelper('Html_Title')->add($this->getView()->lang['title']);

        return $this->getView();
    }
}