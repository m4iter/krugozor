<?php
class Module_Group_Controller_BackendMain extends Module_Group_Controller_BackendCommon
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            $redirect = new Base_Redirect();
            $redirect->setMessage('forbidden_access');
            $redirect->setType('alert');
            $redirect->setRedirectUrl(array('admin'));
            return $redirect->run();
        }

        $this->init();

        $navigation = new Module_Group_Service_Navigation($this->getRequest(),
                                                          $this->getMapper('Group/Group'),
                                                          new Base_Navigation(10, 100));
        $this->getView()->groups = $navigation->getList();
        $this->getView()->navigation = $navigation->getNavigation();

        $this->getView()->field_name = $this->getRequest()->getRequest('field_name');
        $this->getView()->sort_order = $this->getRequest()->getRequest('sort_order');

        return $this->getView();
    }
}