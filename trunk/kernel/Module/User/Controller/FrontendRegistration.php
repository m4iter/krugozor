<?php
class Module_User_Controller_FrontendRegistration extends Module_Common_Controller_Common
{
    private $user;

    public function run()
    {
        parent::common();

        if (!$this->getCurrentUser()->isGuest())
        {
            $redirect = new Base_Redirect();
            $redirect->setHidden(1);
            $redirect->setRedirectUrl('/my/');
            return $redirect->run();
        }

        $this->getView()->session_name = Base_Session::getInstance('CAPTCHASID')->getName();
        $this->getView()->session_id = Base_Session::getInstance('CAPTCHASID')->getId();

        $this->getView()->loadI18n('Common/FrontendGeneral', $this->getVirtualControllerPath());
        $this->getView()->getHelper('Html_Title')->add($this->getView()->lang->title);

        if (!isset($this->user))
        {
            $this->user = self::getMapper('User/User')->createNew();
        }

        if (Http_Request::isPost() && ($result = $this->post()))
        {
            return $result;
        }

        $this->getView()->user = $this->user;

        $this->getView()->current_user = $this->getCurrentUser();

        return $this->getView();
    }

    private function post()
    {
        $this->user = self::getMapper('User/User')->createFromCover
        (
            $this->getRequest()->getPost('user'),
            array('login', 'first_name', 'phone', 'mail', 'icq', 'url', 'country', 'region', 'city')
        );

        // ѕользователь может послать любой числовой ID,
        // который будет транслирован в объект - предотвращаем хак
        $this->user->setId($this->getCurrentUser()->getId());

        $validator = new Validator_Chain('common/general', 'user/registration');

        $validator->addModelErrors($this->user->getValidateErrors());

        $validator->add('captcha', new Module_Common_Validator_Captcha(
            $this->getRequest()->getPost('captcha_code'), Base_Session::getInstance('CAPTCHASID')->code
        ));

        if ($this->user->getLogin())
        {
            $validator->add('login', new Module_User_Validator_UserLoginExists(
                $this->user->getLogin(), $this->user->getId()
            ));
        }

        $validator->add('password_1', new Module_Common_Validator_EmptyNull(
            $this->getRequest()->getRequest('user')->password_1
        ));
        $validator->add('password_1', new Module_Common_Validator_CharPassword(
            $this->getRequest()->getRequest('user')->password_1
        ));

        // $validator->add('user_password_2', new Module_Common_Validator_VarEmpty($this->getRequest()->getRequest()->user->password_2));
        // $validator->add('user_password_2', new Module_Common_Validator_CharPassword($this->getRequest()->getRequest()->user->password_2));
        // $validator->add('user_password_2', new Module_Common_Validator_StringLength($this->getRequest()->getRequest()->user->password_2));

        // if (!empty($this->getRequest()->getRequest()->user->password_1) && !empty($this->getRequest()->getRequest()->user->password_2))
        // {
        //     $validator->add('user_password',
        //                     new Module_User_Validator_UserPasswordsCompare($this->getRequest()->getRequest()->user->password_1,
        //                                                                    $this->getRequest()->getRequest()->user->password_2));
        // }

        if ($this->user->getMail()->getValue())
        {
            $validator->add('mail', new Module_User_Validator_UserMailExists(
                $this->user->getMail()->getValue(), $this->user->getId()
            ));
        }

        $validator->validate();

        if ($this->getView()->err = $validator->getErrors())
        {
            $redirect = new Base_Redirect();
            $redirect->setType('alert');
            $redirect->setHeader('action_failed');
            $redirect->setMessage('post_errors');
            $this->getView()->setRedirect($redirect);

            $this->getView()->password_1 = $this->getRequest()->getRequest('user')->item('password_1');
            // $this->getView()->password_2 = $this->getRequest()->getRequest()->user->password_2;
        }
        else
        {
            $group = self::getMapper('Group/Group')->findGroupByAlias('user');

            if (!$group->getId())
            {
                $validator->addError('system_error', 'SYSTEM_ERROR');
                $this->getView()->err = $validator->getErrors();

                $redirect = new Base_Redirect();
                $redirect->setType('alert');
                $redirect->setMessage('unknown_error');
                $this->getView()->setRedirect($redirect);

                return FALSE;
            }

            $this->user->setPassword($this->getRequest()->getRequest('user')->password_1);

            $this->user->setId(0);
            $this->user->setGroup($group->getId());
            $this->user->setActive(1);
            $this->user->setIp($_SERVER['REMOTE_ADDR']);
            $this->user->setRegdate(new Module_Common_Type_Datetime());

            self::getMapper('User/User')->save($this->user);

            Base_Session::getInstance()->destroy();

            try {
	            if ($this->user->getMail()->getValue())
	            {
	                $sendmail = new Base_Mail();
	                $sendmail->setTo($this->user->getMail()->getValue());
	                $sendmail->setFrom(Base_Registry::getInstance()->config['robot_email_adress']);
	                $sendmail->setReplyTo(Base_Registry::getInstance()->config['robot_email_adress']);
	                $sendmail->setHeader('¬аши регистрационные данные на сайте '.$_SERVER['HTTP_HOST']);
	                $sendmail->setLang('ru');
	                $sendmail->setTemplate($this->getTemplateFilePath('FrontendRegistrationSendData'));
	                $sendmail->user  = $this->user;
	                $sendmail->user_password = $this->getRequest()->getRequest('user')->password_1;
	                $sendmail->send();
	            }
            } catch (Exception $e) {}

            // —разу авторизаци€ пользовател€
            $user = self::getMapper('User/User')->findByLoginPassword(
                $this->user->getLogin(), $this->getRequest()->getRequest('user')->password_1
            );
            $user->setPassword($this->getRequest()->getRequest('user')->password_1);

            if ($user->getId() > 0)
            {
                $time = time()+60*60*24*360;
                $this->getResponse()->setcookie('auth_id', $user->getId(), $time, '/');
                $this->getResponse()->setcookie('auth_hash',
	                                            md5($user->getLogin().
	                                                $user->getPassword().
	                                                Base_Registry::getInstance()->config->user_cookie_salt),
	                                            $time,
	                                            '/');
                $this->getResponse()->sendCookie();

                $redirect = new Base_Redirect();
                $redirect->setHeader('you_registration_ok');
                $redirect->setMessage($this->user->getMail()->getValue()
                                      ? 'you_registration_with_email'
                                      : 'you_registration_without_email'
                                     );
                $redirect->addParam('login', $user->getLogin());
                $redirect->addParam('password', $this->getRequest()->getRequest()->user->password_1);
                $redirect->setRedirectUrl('/my/adverts/edit/?from_registration=1');
                return $redirect->run();
            }

            // пользователь  не вставилс€
            $redirect = new Base_Redirect();
            $redirect->setType('alert');
            $redirect->setHeader('action_failed');
            $redirect->setMessage('unknown_error');
            $redirect->setRedirectUrl($this->getRequest()->getRequest()->getUri());
            return $redirect->run();
        }
    }
}
?>