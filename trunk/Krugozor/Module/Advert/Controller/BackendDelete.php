<?php
class Module_Advert_Controller_BackendDelete extends Module_Advert_Controller_BackendCommon
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            $redirect = new Base_Redirect($this->getDb());
            $redirect->setMessage('forbidden_access');
            $redirect->setType('alert');
            $redirect->setRedirectUrl(array('admin', 'advert'));
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
            $redirect->setMessage('id_element_not_exists');
            $redirect->setRedirectUrl(array('admin', 'advert'));
            return $redirect->run();
        }

        $this->getMapper('Advert/Advert')->deleteById($this->advert);

        $redirect = new Base_Redirect($this->getDb());
        $redirect->setMessage('element_delete');
        $redirect->setRedirectUrl($this->getRequest()->getRequest('referer'));
        return $redirect->run();
    }
}
?>