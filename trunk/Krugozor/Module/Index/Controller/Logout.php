<?php
class Module_Index_Controller_Logout extends Module_Common_Controller_Common
{
    public function run()
    {
        parent::common();

        if ($this->getCurrentUser())
        {
            $this->destroyCurrentUser();
        }

        $redirect = new Base_Redirect($this->getDb());
        $redirect->setHeader('action_complete');
        $redirect->setMessage('outside_system');
        $redirect->setRedirectUrl($this->getRequest()->getRequest('referer') ?: '/');
        return $redirect->run();
    }
}