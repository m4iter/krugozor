<?php
class Module_User_Controller_BackendDelete extends Module_User_Controller_BackendCommon
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            return $this->createNotification()
                        ->setMessage('forbidden_access')
                        ->setType('alert')
                        ->setRedirectUrl(array('admin', 'user'))
                        ->run();
        }

        if ($result = $this->checkIdOnValid())
        {
            return $result;
        }

        if (empty($this->getRequest()->getRequest()->id))
        {
            return $this->createNotification()
                        ->setType('alert')
                        ->setMessage('id_user_not_exists')
                        ->setRedirectUrl(array('admin', 'user'))
                        ->run();
        }

        $this->getMapper('User/User')->delete($this->user);

        return $this->createNotification()
                    ->setMessage('user_delete')
                    ->addParam('user_name', Helper_Format::hsc($this->user->getFullName()))
                    ->setRedirectUrl($this->getRequest()->getRequest('referer'))
                    ->run();
    }
}