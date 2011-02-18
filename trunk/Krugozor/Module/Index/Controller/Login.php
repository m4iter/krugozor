<?php
class Module_Index_Controller_Login extends Module_Common_Controller_Common
{
    public function run()
    {
        parent::common();

        // Определяем имя шаблона - Frontend или Backend
        $template = $this->getRequest()->getRequest()->isFrontend()
                    ? 'FrontendLogin'
                    : 'BackendLogin';

        // Определяем имя шаблона - для какого пользователя
        if ($this->getRequest()->getRequest()->isFrontend())
        {
        	$template .= $this->getCurrentUser()->isGuest() ? '_UserOut' : '_UserIn';
        }
        else
        {
        	$template .= $this->checkAccess('Index', 'Index') ? '_UserIn' : '_UserOut';
        }

        $this->getView($template);

        $general_lang_file = $this->getRequest()->getRequest()->isFrontend()
                             ? 'FrontendGeneral'
                             : 'BackendGeneral';

        $index_lang_file = $this->getRequest()->getRequest()->isFrontend()
                             ? 'FrontendLogin'
                             : 'BackendLogin';

        $this->getView()->loadI18n('common/'.$general_lang_file,
                                   'index/'.$index_lang_file);

        $this->getView()->getHelper('Html_Title')->add($this->getView()->lang['title']);

        $user = $this->getMapper('User/User')->createNew();

        if (Http_Request::isPost())
        {
            $user = $this->getMapper('User/User')->createFromCover(
                $this->getRequest()->getRequest('user'),  array('login', 'password')
            );

            $validator = new Validator_Chain('common/general', 'index/login');

            $validator->addModelErrors($user->getValidateErrors());

            $validator->add('password', new Module_Common_Validator_EmptyNull($user->getPassword()));

            $validator->validate();

            if (!$this->getView()->err = $validator->getErrors())
            {
                $user = $this->getMapper('User/User')->findByLoginPassword(
                    $user->getLogin(), $this->getRequest()->getRequest('user')->password
                );

                $user->setPassword($this->getRequest()->getRequest('user')->password);

                if ($user->getId() > 0)
                {
                    $time = 0;

                    if ($this->getRequest()->getPost('autologin', 'decimal') &&
                        $days = $this->getRequest()->getPost('ml_autologin', 'decimal'))
                    {
                        $time = time()+60*60*24*$days;
                    }

                    $this->getResponse()->setcookie('auth_id', $user->getId(), $time, '/');
                    $this->getResponse()->setcookie('auth_hash', md5(
                        $user->getLogin() .
                        $user->getPassword() .
                        Base_Registry::getInstance()->config['user_cookie_salt']),
                        $time, '/'
                    );

                    if ($this->getRequest()->getRequest()->isFrontend())
                    {
                        $referer = '/my/';
                    }
                    else
                    {
	                    $referer = !empty($this->getRequest()->getRequest()->referer)
	                               ? $this->getRequest()->getRequest('referer')
	                               : '/admin/';
                    }

                    $redirect = new Base_Redirect($this->getDb());
                    $redirect->setMessage('inside_system');
                    $redirect->setRedirectUrl($referer);
                    return $redirect->run();
                }
                else
                {
                    $validator->addError('authorization', 'INCORRECT_AUTH_DATA');

                    $this->getView()->err = $validator->getErrors();

                    if ($this->getRequest()->getRequest()->isFrontend())
                    {
                        $redirect = new Base_Redirect($this->getDb());
                        $redirect->setType('alert');
                        $redirect->setHeader('action_failed');
                        $redirect->setMessage('post_errors');
                        $this->getView()->setRedirect($redirect);
                    }
                }
            }

            $this->getView()->autologin = $this->getRequest()->getRequest('autologin');
            $this->getView()->ml_autologin = $this->getRequest()->getRequest('ml_autologin');
        }
        else
        {
            $this->getView()->autologin = 0;
            $this->getView()->ml_autologin = 365;
        }

        $this->getView()->current_user = $this->getCurrentUser();
        $this->getView()->referer = $this->getRequest()->getRequest('referer');
        $this->getView()->user = $user;

        return $this->getView();
    }
}