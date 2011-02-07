<?php
abstract class Module_Module_Controller_CommonModule extends Module_Common_Controller_Common
{
    protected $module;

    protected function checkIdOnValid()
    {
        if ($id = $this->getRequest()->getRequest('id'))
        {
            if (!Base_Numeric::is_decimal($id))
            {
                $redirect = new Base_Redirect($this->getDb());
                $redirect->setType('alert');
                $redirect->setMessage('bad_id_element');
                $redirect->setRedirectUrl(array('admin', 'module'));
                return $redirect->run();
            }

            $this->module = $this->getMapper('Module/Module')->findById(
                $this->getRequest()->getRequest('id')
            );

            if (!$this->module->getId())
            {
                $redirect = new Base_Redirect($this->getDb());
                $redirect->setType('alert');
                $redirect->setMessage('element_does_not_exist');
                $redirect->setRedirectUrl(array('admin', 'module'));
                return $redirect->run();
            }
        }
    }

    protected function init()
    {
        $this->getView()->loadI18n('Common/BackendGeneral', $this->getVirtualControllerPath());

        $this->getView()->getHelper('Html_Title')->add($this->getView()->lang['title']);
    }
}
?>