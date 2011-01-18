<?php
abstract class Module_Module_Controller_CommonController extends Module_Common_Controller_Common
{
    protected $controller;

    protected $module;

    protected function checkIdOnValid()
    {
        if ($id = $this->getRequest()->getRequest('id'))
        {
            $this->controller = self::getMapper('Module/Controller')->findById(
                $this->getRequest()->getRequest('id')
            );

            if (!$this->controller->getId())
            {
                $redirect = new Base_Redirect();
                $redirect->setType('alert');
                $redirect->setMessage('element_does_not_exist');
                $redirect->setRedirectUrl(Base_Redirect::implode('admin', 'module','edit')
                                          .'?id='.$this->getRequest()->getRequest('id_module'));
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