<?php
class Module_User_Controller_FrontendEdit extends Module_Common_Controller_Common
{
    private $user;

    public function run()
    {
        parent::common();

        if ($this->getCurrentUser()->isGuest())
        {
            $redirect = new Base_Redirect($this->getDb());
            $redirect->setHidden(1);
            $redirect->setRedirectUrl('/my/');
            return $redirect->run();
        }
        else if (!$this->checkAccess())
        {
            $redirect = new Base_Redirect($this->getDb());
            $redirect->setMessage('forbidden_access');
            $redirect->setType('alert');
            $redirect->setRedirectUrl(array('my'));
            return $redirect->run();
        }

        $this->getView()->current_user = $this->user = $this->getCurrentUser();
        $this->getView()->loadI18n('Common/FrontendGeneral', $this->getVirtualControllerPath());

        if (Http_Request::isPost() && ($result = $this->post()))
        {
            return $result;
        }

        $this->getView()->getHelper('Html_Title')->add($this->getView()->lang->title);
        $this->getView()->sex_types = Module_User_Type_Sex::getTypes();
        $this->getView()->user = $this->user;

        return $this->getView();
    }

    protected function post()
    {
        $this->user = $this->getMapper('User/User')->createFromCover
        (
            $this->getRequest()->getPost('user'),
            array('login', 'first_name', 'phone', 'mail', 'url', 'icq',
                  'country', 'region', 'city', 'sex', 'age_day',
                  'age_month', 'age_year', 'id')
        );

        // ƒаем пон€ть, что работаем с текущим пользователем.
        $this->user->setId($this->getCurrentUser()->getId());

        $validator = new Validator_Chain('common/general', 'user/registration');

        $validator->addModelErrors($this->user->getValidateErrors());

        if ($this->user->getLogin())
        {
            $validator->add('login', new Module_User_Validator_UserLoginExists(
                $this->user, $this->getMapper('User/User')
            ));
        }

        if ($this->user->getLogin() !== $this->getCurrentUser()->getLogin() &&
            $this->getRequest()->getRequest('user')->password_1 == '')
        {
            $validator->addError('login', 'CHANGE_LOGIN_WITH_PASSWORD');
        }

        if ($this->getRequest()->getRequest('user')->password_1 != '')
        {
            $validator->add('password_1', new Module_Common_Validator_CharPassword($this->getRequest()->getRequest()->user->password_1));
        }

        /*if ($this->getRequest()->getRequest()->user->password_2)
        {
            $validator->add('user_password_2', new Module_Common_Validator_CharPassword($this->getRequest()->getRequest()->user->password_2));
        }*/

        /*if (!empty($this->getRequest()->getRequest()->user->password_1) && !empty($this->getRequest()->getRequest()->user->password_2))
        {
            $validator->add('user_password',
                            new Module_User_Validator_UserPasswordsCompare($this->getRequest()->getRequest()->user->password_1,
                                                                           $this->getRequest()->getRequest()->user->password_2));
        }*/

        if ($this->user->getMail()->getValue())
        {
            $validator->add('mail', new Module_User_Validator_UserMailExists(
                $this->user, $this->getMapper('User/User'))
            );
        }

        $validator->validate();

        if ($this->getView()->err = $validator->getErrors())
        {
            $redirect = new Base_Redirect($this->getDb());
            $redirect->setType('alert');
            $redirect->setMessage('post_errors');
            $this->getView()->setRedirect($redirect);

            $this->getView()->password_1 = $this->getRequest()->getRequest()->user->password_1;
            //$this->getView()->password_2 = $this->getRequest()->getRequest()->user->password_2;
        }
        else
        {
            if ($this->getRequest()->getRequest()->user->password_1)
            {
                $this->user->setPassword($this->getRequest()->getRequest()->user->password_1);
            }

            $this->getMapper('User/User')->save($this->user);

            // помен€ли пароль
            if ($this->user->getPassword())
            {
                // —разу авторизаци€ пользовател€
                $user = $this->getMapper('User/User')->findByLoginPassword($this->user->getLogin(), $this->getRequest()->getRequest()->user->password_1);
                $user->setPassword($this->getRequest()->getRequest()->user->password_1);

	            if ($user->getId() > 0)
	            {
	                $time = time()+60*60*24*360;
	                $this->getResponse()->setcookie('auth_id', $user->getId(), $time, '/');
	                $this->getResponse()->setcookie('auth_hash',
	                                           md5($user->getLogin().
	                                               $user->getPassword().
	                                               Base_Registry::getInstance()->config['user_cookie_salt']),
	                                           $time,
	                                           '/');
	                $this->getResponse()->sendCookie();
	            }
            }

            $redirect = new Base_Redirect($this->getDb());
            $redirect->setMessage('data_saved');
            $redirect->setRedirectUrl($this->getRequest()->getRequest()->getUri());
            return $redirect->run();
        }
    }
}
?>