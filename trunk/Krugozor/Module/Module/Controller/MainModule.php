<?php
class Module_Module_Controller_MainModule extends Module_Module_Controller_CommonModule
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            return $this->createNotification()
                        ->setMessage('forbidden_access')
                        ->setType('alert')
                        ->setRedirectUrl(array('admin'))
                        ->run();
        }

        $this->init();

        $navigation = new Module_Module_Service_Navigation($this->getRequest(),
                                                           $this->getMapper('Module/Module'),
                                                           new Base_Navigation(10, 100));
        $this->getView()->modules = $navigation->getList();
        $this->getView()->navigation = $navigation->getNavigation();

        $this->getView()->field_name = $this->getRequest()->getRequest('field_name');
        $this->getView()->sort_order = $this->getRequest()->getRequest('sort_order');

        return $this->getView();
    }
}