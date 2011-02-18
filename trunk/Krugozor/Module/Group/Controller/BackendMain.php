<?php
class Module_Group_Controller_BackendMain extends Module_Group_Controller_BackendCommon
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

        $navigation = new Module_Group_Service_Navigation(
            $this->getRequest(),
            $this->getMapper('Group/Group'),
            new Base_Navigation(10, 100)
        );

        $this->getView()->groups = $navigation->getList();
        $this->getView()->navigation = $navigation->getNavigation();
        $this->getView()->field_name = $this->getRequest()->getRequest('field_name');
        $this->getView()->sort_order = $this->getRequest()->getRequest('sort_order');

        return $this->getView();
    }
}