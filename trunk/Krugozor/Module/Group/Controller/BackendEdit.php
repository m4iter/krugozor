<?php
class Module_Group_Controller_BackendEdit extends Module_Group_Controller_BackendCommon
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            return $this->createNotification()
                        ->setMessage('forbidden_access')
                        ->setType('alert')
                        ->setRedirectUrl(array('admin', 'group'))
                        ->run();
        }

        if ($result = $this->checkIdOnValid())
        {
            return $result;
        }

        $this->init();

        if (empty($this->group))
        {
            $this->group = $this->getMapper('Group/Group')->createNew();
        }

        if (Http_Request::isPost() && ($result = $this->post()))
        {
            return $result;
        }

        $this->getView()->group = $this->group;
        $this->getView()->modules = $this->getMapper('Module/Module')->findModulesWithControllers();
        $this->getView()->return_on_page = $this->getRequest()->getRequest('return_on_page');
        
        return $this->getView();
    }

    protected function post()
    {
        $this->group = $this->getMapper('Group/Group')->createFromCover(
            $this->getRequest()->getPost('group')
        );

        $validator = new Validator_Chain('common/general');
        $validator->addModelErrors($this->group->getValidateErrors());

        $notification = $this->createNotification();

        if ($this->getView()->err = $validator->getErrors())
        {
            $notification->setType('alert')
                         ->setMessage('post_errors');
            $this->getView()->setRedirect($notification);

            return false;
        }
        else
        {
            $this->getMapper('Group/Group')->save($this->group);

            return $notification
                     ->setMessage('group_edit_ok')
                     ->addParam('group_name', Helper_Format::hsc($this->group->getName()))
                     ->addParam('id', $this->group->getId())
                     ->setRedirectUrl($this->getRequest()->getRequest('return_on_page')
                                  ? Base_Redirect::implode('admin', 'group', 'edit').
                                    '?id=' . $this->group->getId()
                                  : (
                                        $this->getRequest()->getRequest('referer')
                                        ? $this->getRequest()->getRequest('referer')
                                        : array('admin', 'group')
                                    )
                                 )
                     ->run();
        }
    }
}