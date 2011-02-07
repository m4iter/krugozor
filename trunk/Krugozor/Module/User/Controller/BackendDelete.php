<?php
class Module_User_Controller_BackendDelete extends Module_User_Controller_BackendCommon
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            $redirect = new Base_Redirect($this->getDb());
            $redirect->setMessage('forbidden_access');
            $redirect->setType('alert');
            $redirect->setRedirectUrl(array('admin', 'user'));
            return $redirect->run();
        }

        if ($result = $this->checkIdOnValid())
        {
            return $result;
        }

        if (empty($this->getRequest()->getRequest()->id))
        {
            $redirect = new Base_Redirect($this->getDb());
            $redirect->setType('alert');
            $redirect->setMessage('id_user_not_exists');
            $redirect->setRedirectUrl(array('admin', 'user'));
            return $redirect->run();
        }

        $this->getMapper('User/User')->delete($this->user);

        $redirect = new Base_Redirect($this->getDb());
        $redirect->setMessage('user_delete');
        $redirect->addParam('user_name', Helper_Format::hsc($this->user->getFullName()));
        $redirect->setRedirectUrl($this->getRequest()->getRequest('referer'));
        return $redirect->run();
    }
}
?>