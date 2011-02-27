<?php
class Module_User_Controller_FrontendEdit extends Module_Common_Controller_Common
{
    private $user;

    public function run()
    {
        parent::common();

        if ($this->getCurrentUser()->isGuest())
        {
            return $this->createNotification()
                        ->setHidden(1)
                        ->setRedirectUrl('/my/')
                        ->run();
        }
        else if (!$this->checkAccess())
        {
            return $this->createNotification()
                        ->setMessage('forbidden_access')
                        ->setType('alert')
                        ->setRedirectUrl(array('my'))
                        ->run();
        }

        $this->getView()->current_user = $this->user = $this->getCurrentUser();
        $this->getView()->loadI18n('Common/FrontendGeneral', $this->getVirtualControllerPath());

        if (Http_Request::isPost() && ($result = $this->post()))
        {
            return $result;
        }

        $this->getView()->getHelper('Html_Title')->add($this->getView()->lang['title']);
        $this->getView()->sex_types = Module_User_Type_Sex::getTypes();
        $this->getView()->user = $this->user;

        return $this->getView();
    }

    protected function post()
    {
        $this->user = $this->getMapper('User/User')->createFromCover
        (
            $this->getRequest()->getPost('user'),
            array('login', 'first_name', 'phone', 'mail', 'url', 'icq', 'country', 'region', 'city',
                  'sex', 'age_day', 'age_month', 'age_year', 'id')
        );

        // Даем понять, что работаем с текущим пользователем.
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
            $validator->add('password_1', new Module_Common_Validator_CharPassword
            (
                $this->getRequest()->getRequest('user')->password_1)
            );
        }

        if ($this->user->getMail()->getValue())
        {
            $validator->add('mail', new Module_User_Validator_UserMailExists
            (
                $this->user, $this->getMapper('User/User'))
            );
        }

        $validator->validate();

        if ($this->getView()->err = $validator->getErrors())
        {
            $this->getView()->setRedirect
            (
                $this->createNotification()
                     ->setType('alert')
                     ->setMessage('post_errors')
            );

            $this->getView()->password_1 = $this->getRequest()->getRequest('user')->password_1;
        }
        else
        {
            // Если требуется изменить пароль, явно указываем его для объекта.
            if ($this->getRequest()->getRequest('user')->password_1)
            {
                $this->user->setPassword($this->getRequest()->getRequest('user')->password_1);
            }

            $this->getMapper('User/User')->save($this->user);

            // Если поменяли пароль, то нужно сделать авторизацию заново.
            if ($this->getRequest()->getRequest('user')->password_1)
            {
                $user = $this->getMapper('User/User')->findByLoginPassword(
                    $this->user->getLogin(), $this->getRequest()->getRequest('user')->password_1
                );

	            if ($user->getId() > 0)
	            {
	                $time = time() + 60 * 60 * 24 * 360;
	                $this->getResponse()->setcookie('auth_id', $user->getId(), $time, '/');
	                $this->getResponse()->setcookie('auth_hash', md5($user->getLogin() . $user->getPassword()), $time, '/');
	                $this->getResponse()->sendCookie();
	            }
            }

            return $this->createNotification()
                        ->setMessage('data_saved')
                        ->setRedirectUrl($this->getRequest()->getRequest()->getUri())
                        ->run();
        }
    }
}