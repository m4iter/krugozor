<?php
class Module_Module_Controller_DeleteController extends Module_Module_Controller_CommonController
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            $redirect = new Base_Redirect($this->getDb());
            $redirect->setMessage('forbidden_access');
            $redirect->setType('alert');
            $redirect->setRedirectUrl(Base_Redirect::implode('admin','module', 'edit').
                                      '?id='.$this->getRequest()->getRequest('id_module'));
            return $redirect->run();
        }

        if ($result = $this->checkIdOnValid())
        {
            return $result;
        }

        $this->getMapper('Module/Controller')->deleteById($this->controller);

        $redirect = new Base_Redirect($this->getDb());
        $redirect->setMessage('element_delete');
        $redirect->setRedirectUrl(Base_Redirect::implode('admin', 'module', 'edit').
                                  '?id='.$this->getRequest()->getRequest('id_module')
                                 );
        return $redirect->run();
    }
}
?>