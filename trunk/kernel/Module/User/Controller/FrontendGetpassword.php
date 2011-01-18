<?php
class Module_User_Controller_FrontendGetpassword extends Module_Common_Controller_Common
{
    public function run()
    {
        $this->getView()->loadI18n('Common/FrontendGeneral', $this->getVirtualControllerPath());

        $this->getView()->getHelper('Html_Title')->add($this->getView()->lang['title']);

        $this->getView()->current_user = $this->getCurrentUser();

        if (Http_Request::isPost() && ($result = $this->post()))
        {
            return $result;
        }

        return $this->getView();
    }

    private function post()
    {
        $validator = new Validator_Chain('common/general', 'user/getpassword');

        if ($user_login = $this->getRequest()->getPost('user')->login)
        {
            $validator->add('user_login', new Module_Common_Validator_StringLength($user_login));
            $validator->add('user_login', new Module_Common_Validator_CharPassword($user_login));
        }
        else if ($user_mail = $this->getRequest()->getPost('user')->mail)
        {
            $validator->add('user_mail', new Module_Common_Validator_StringLength($user_mail));
            $validator->add('user_mail', new Module_Common_Validator_Email($user_mail));
        }
        else
        {
            $validator->addError('common_error', 'NON_EXIST_TEST_DATA');
        }

        $validator->validate();

        if ($this->getView()->err = $validator->getErrors())
        {
            $redirect = new Base_Redirect();
            $redirect->setType('alert');
            $redirect->setHeader('action_failed');
            $redirect->setMessage('post_errors');
            $this->getView()->setRedirect($redirect);
        }
        else
        {
	        if (!empty($user_login))
	        {
	            $user = self::getMapper('User/User')->findByLogin($user_login);
	        }
	        else if (!empty($user_mail))
	        {
	            $user = self::getMapper('User/User')->findByMail($user_mail);
	        }

	        if (!$user->getId())
	        {
	            $redirect = new Base_Redirect();
                $redirect->setType('alert');
                $redirect->setHeader('action_failed');
                $redirect->setMessage('user_not_exist_message');
                $this->getView()->setRedirect($redirect);
	        }
	        elseif (!$user->getMail()->getValue())
	        {
                $redirect = new Base_Redirect();
                $redirect->setType('alert');
                $redirect->setHeader('action_failed');
                $redirect->setMessage('user_mail_not_exist_message');
                $this->getView()->setRedirect($redirect);
	        }
            else
            {
                $mail = new Base_Mail();
                $mail->setFrom(Base_Registry::getInstance()->config['robot_email_adress']);
                $mail->setReplyTo(Base_Registry::getInstance()->config['robot_email_adress']);
                $mail->setHeader('¬осстановление забытого парол€ на сайте '.$_SERVER['HTTP_HOST']);
                $mail->setTemplate($this->getTemplateFilePath('FrontendGetpasswordSendTest'));

	            try
	            {
	                $service = new Module_User_Service_Getpassword();
	                $service->setUser($user)->setMail($mail)->sendEmailWithHash();

	                $redirect = new Base_Redirect();
                    $redirect->setMessage('test_send_ok_message');
                    $redirect->setRedirectUrl($this->getRequest()->getRequest()->getUri());
                    return $redirect->run();
	            }
	            catch (Exception $e)
	            {
                    $validator->addError('common_error', 'SYSTEM_ERROR', array('error_message' => $e->getMessage()));

	                $this->getView()->err = $validator->getErrors();

	                $redirect = new Base_Redirect();
	                $redirect->setType('alert');
	                $redirect->setHeader('action_failed');
	                $redirect->setMessage('unknown_error');
	                $this->getView()->setRedirect($redirect);
	            }
            }
        }

        $this->getView()->user_login = $this->getRequest()->getPost('user')->login;
        $this->getView()->user_mail = $this->getRequest()->getPost('user')->mail;
    }
}
?>