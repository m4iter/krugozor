<?php
abstract class Module_Module_Controller_CommonController extends Module_Common_Controller_Common
{
    protected $controller;

    protected $module;

    protected function checkIdOnValid()
    {
        if ($id = $this->getRequest()->getRequest('id'))
        {
            $this->controller = $this->getMapper('Module/Controller')->findById($id);

            if (!$this->controller->getId())
            {
                return $this->createNotification()
                            ->setType('alert')
                            ->setMessage('element_does_not_exist')
                            ->setRedirectUrl(Base_Redirect::implode('admin', 'module', 'edit')
                                             . '?id=' . $this->getRequest()->getRequest('id_module'))
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