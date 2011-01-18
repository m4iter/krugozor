<?php
class Module_User_Controller_BackendEdit extends Module_User_Controller_BackendCommon
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            $redirect = new Base_Redirect();
            $redirect->setMessage('forbidden_access');
            $redirect->setType('alert');
            $redirect->setRedirectUrl(array('admin', 'user'));
            return $redirect->run();
        }

        if ($result = $this->checkIdOnValid())
        {
            return $result;
        }

        $this->init();

        if (empty($this->user))
        {
            $this->user = self::getMapper('User/User')->createNew();
        }

        if (Http_Request::isPost() && ($result = $this->post()))
        {
            return $result;
        }

        $this->getView()->user = $this->user;
        $this->getView()->groups = self::getMapper('Group/Group')->getAllGroupsWithoutGuest();
        $this->getView()->sex_types = Module_User_Type_Sex::getTypes();
        $this->getView()->return_on_page = $this->getRequest()->getRequest('return_on_page');

        return $this->getView();
    }

    protected function post()
    {
        $this->user = self::getMapper('User/User')->createFromCover
        (
            $this->getRequest()->getPost('user'),
            array('id', 'active', 'group', 'login', 'mail',
                  'first_name', 'last_name', 'sex', 'country',
                  'city', 'region', 'phone', 'icq', 'url', 'regdate')
        );

        $validator = new Validator_Chain('common/general', 'user/registration');

        $validator->addModelErrors($this->user->getValidateErrors());

        if ($this->user->getLogin())
        {
            $validator->add('login', new Module_User_Validator_UserLoginExists(
                $this->user->getLogin(), $this->user->getId())
            );
        }

        if (!$this->user->getId())
        {
            $validator->add('password_1', new Module_Common_Validator_Empty(
                $this->getRequest()->getRequest()->user->password_1)
            );
            $validator->add('password_1', new Module_Common_Validator_CharPassword(
                $this->getRequest()->getRequest()->user->password_1)
            );

            $validator->add('password_2', new Module_Common_Validator_Empty(
                $this->getRequest()->getRequest()->user->password_2)
            );
            $validator->add('password_2', new Module_Common_Validator_CharPassword(
                $this->getRequest()->getRequest()->user->password_2)
            );
        }

        if (!empty($this->getRequest()->getRequest()->user->password_1) &&
            !empty($this->getRequest()->getRequest()->user->password_2))
        {
            $validator->add('password',
                            new Module_User_Validator_UserPasswordsCompare(
                                $this->getRequest()->getRequest()->user->password_1,
                                $this->getRequest()->getRequest()->user->password_2
                                )
                           );
        }

        if ($this->user->getMail()->getValue())
        {
            $validator->add('user_mail', new Module_User_Validator_UserMailExists(
                $this->user->getMail()->getValue(), $this->user->getId()
            ));
        }

        $validator->validate();

        if ($this->getView()->err = $validator->getErrors())
        {
            $redirect = new Base_Redirect();
            $redirect->setType('alert');
            $redirect->setMessage('post_errors');
            $this->getView()->setRedirect($redirect);

            $this->getView()->password_1 = $this->getRequest()->getRequest('user')->password_1;
            $this->getView()->password_2 = $this->getRequest()->getRequest('user')->password_2;
        }
        else
        {
	        if (!empty($this->getRequest()->getRequest('user')->password_1) &&
	            !empty($this->getRequest()->getRequest('user')->password_2))
	        {
	            $this->user->setPassword($this->getRequest()->getRequest('user')->password_1);
	        }

            self::getMapper('User/User')->save($this->user);

            $redirect = new Base_Redirect();
            $redirect->setMessage('user_edit_ok');
            $redirect->addParam('user_name', Helper_Format::hsc( $this->user->getFullName() ));
            $redirect->addParam('id_user', $this->user->getId());
            $redirect->setRedirectUrl($this->getRequest()->getRequest('return_on_page')
                                      ? Base_Redirect::implode('admin', 'user', 'edit').'?id='.$this->user->getId()
                                      : (
                                            $this->getRequest()->getRequest('referer')
                                            ? $this->getRequest()->getRequest('referer')
                                            : array('admin', 'user')
                                        )
                                     );
            return $redirect->run();
        }

        return false;
    }
}