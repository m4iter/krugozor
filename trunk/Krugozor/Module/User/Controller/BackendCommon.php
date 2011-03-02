<?php
abstract class Module_User_Controller_BackendCommon extends Module_Common_Controller_Common
{
    protected $user;

    protected function checkIdOnValid()
    {
        if ($id = $this->getRequest()->getRequest('id'))
        {
            if (!Base_Numeric::is_decimal($id))
            {
                return $this->createNotification()
                            ->setType('alert')
                            ->setMessage('bad_id_user')
                            ->setRedirectUrl(array('admin', 'user'))
                            ->run();
            }

            $this->user = $this->getMapper('User/User')->findById(
                $this->getRequest()->getRequest('id')
            );

            if (!$this->user->getId())
            {
                return $this->createNotification()
                            ->setType('alert')
                            ->setMessage('user_does_not_exist')
                            ->addParam('id_user', $this->getRequest()->getRequest('id'))
                            ->setRedirectUrl(array('admin', 'user'))
                            ->run();
            }
        }
    }

    protected function init()
    {
        $this->getView()->loadI18n('Common/BackendGeneral', $this->getVirtualControllerPath());

        $this->getView()->getHelper('Html_Title')->add($this->getView()->lang['title']);
    }
}