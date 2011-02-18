<?php
class Module_Module_Controller_DeleteModule extends Module_Module_Controller_CommonModule
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            $redirect = new Base_Redirect($this->getDb());
            $redirect->setMessage('forbidden_access');
            $redirect->setType('alert');
            $redirect->setRedirectUrl(array('admin','module'));
            return $redirect->run();
        }

        if ($result = $this->checkIdOnValid())
        {
            return $result;
        }

        if (!isset($this->getRequest()->getRequest()->id))
        {
            $redirect = new Base_Redirect($this->getDb());
            $redirect->setType('alert');
            $redirect->setMessage('id_element_not_exists');
            $redirect->setRedirectUrl(array('admin', 'module'));
            return $redirect->run();
        }

        $this->getMapper('Module/Module')->delete($this->module);

        $redirect = new Base_Redirect($this->getDb());
        $redirect->setMessage('element_delete');
        $redirect->setRedirectUrl($this->getRequest()->getRequest()->referer);
        return $redirect->run();
    }
}
?>