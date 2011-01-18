<?php
class Module_Group_Controller_BackendEdit extends Module_Group_Controller_BackendCommon
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            $redirect = new Base_Redirect();
            $redirect->setMessage('forbidden_access');
            $redirect->setType('alert');
            $redirect->setRedirectUrl(Base_Redirect::implode('admin', 'group'));
            return $redirect->run();
        }

        if ($result = $this->checkIdOnValid())
        {
            return $result;
        }

        $this->init();

        if (empty($this->group))
        {
            $this->group = self::getMapper('Group/Group')->createNew();
        }

        if (Http_Request::isPost() && ($result = $this->post()))
        {
            return $result;
        }

        $this->getView()->group = $this->group;

        $this->getView()->groups = self::getMapper('Group/Group')->getAllGroupsWithoutGuest();

        $this->getView()->return_on_page = $this->getRequest()->getRequest('return_on_page');

        $this->getView()->modules = self::getMapper('Module/Module')->loadModulesWithControllers();

        return $this->getView();
    }

    protected function post()
    {
        $this->group = self::getMapper('Group/Group')->createFromCover(
            $this->getRequest()->getPost('group')
        );

        $this->group->setRules($this->getRequest()->getPost('group')->group_access);

        $validator = new Validator_Chain('common/general');

        $validator->addModelErrors($this->group->getValidateErrors());

        if ($this->getView()->err = $validator->getErrors())
        {
            $redirect = new Base_Redirect();
            $redirect->setType('alert');
            $redirect->setMessage('post_errors');
            $this->getView()->setRedirect($redirect);

            return false;
        }
        else
        {
            self::getMapper('Group/Group')->save($this->group);

	        if ($this->getRequest()->getPost('group')->group_access)
	        {
	            $access = new Base_Access();
	            $access->clearGroupRulesById($this->group->getId());
	            $access->saverGroupRulesById($this->group->getId(),
	                                         $this->getRequest()->getPost('group')->group_access);
	        }

            $redirect = new Base_Redirect();
            $redirect->setMessage('group_edit_ok');
            $redirect->addParam('group_name', Helper_Format::hsc( $this->group->getName()));
            $redirect->addParam('id', $this->group->getId());
            $redirect->setRedirectUrl($this->getRequest()->getRequest('return_on_page')
                                      ? Base_Redirect::implode('admin', 'group', 'edit').
                                        '?id='.$this->group->getId()
                                      : (
                                            $this->getRequest()->getRequest('referer')
                                            ? $this->getRequest()->getRequest('referer')
                                            : array('admin', 'group')
                                        )
                                     );
            return $redirect->run();
        }
    }
}