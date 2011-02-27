<?php
class Module_Index_Controller_Logout extends Module_Common_Controller_Common
{
    public function run()
    {
        parent::common();

        if (!$this->getCurrentUser()->isGuest())
        {
            $this->destroyCurrentUser();
        }

        return $this->createNotification()
                    ->setHeader('action_complete')
                    ->setMessage('outside_system')
                    ->setRedirectUrl($this->getRequest()->getRequest('referer') ?: '/')
                    ->run();
    }
}