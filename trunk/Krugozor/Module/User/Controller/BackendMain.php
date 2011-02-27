<?php
class Module_User_Controller_BackendMain extends Module_User_Controller_BackendCommon
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

        $navigation = new Module_User_Service_Navigation($this->getRequest(),
                                                         $this->getMapper('User/User'),
                                                         new Base_Navigation(10, 100));
        $this->getView()->users = $navigation->getList();
        $this->getView()->navigation = $navigation->getNavigation();

        $this->getView()->field_name = $this->getRequest()->getRequest('field_name');
        $this->getView()->sort_order = $this->getRequest()->getRequest('sort_order');

        $this->getView()->search = $this->getRequest()->getRequest('search');
        $this->getView()->col = $this->getRequest()->getRequest('col');
        $this->getView()->user_active = $this->getRequest()->getRequest('user_active');
        $this->getView()->user_country = $this->getRequest()->getRequest('user_country');
        $this->getView()->user_region = $this->getRequest()->getRequest('user_region');
        $this->getView()->user_city = $this->getRequest()->getRequest('user_city');

        return $this->getView();
    }
}