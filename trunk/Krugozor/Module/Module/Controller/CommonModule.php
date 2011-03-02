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
                return $this->createNotification()
                            ->setType('alert')
                            ->setMessage('bad_id_element')
                            ->setRedirectUrl(array('admin', 'module'))
                            ->run();
            }

            $this->module = $this->getMapper('Module/Module')->findById(
                $this->getRequest()->getRequest('id')
            );

            if (!$this->module->getId())
            {
                return $this->createNotification()
                            ->setType('alert')
                            ->setMessage('element_does_not_exist')
                            ->setRedirectUrl(array('admin', 'module'))
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