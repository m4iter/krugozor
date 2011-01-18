<?php
class Module_User_Controller_FrontendGetpasswordEnd extends Module_Common_Controller_Common
{
    public function run()
    {
        $this->getView()->loadI18n('Common/FrontendGeneral', $this->getVirtualControllerPath());

        $this->getView()->getHelper('Html_Title')->add($this->getView()->lang['title']);

        $this->getView()->current_user = $this->getCurrentUser();

        try
        {
            $service = new Module_User_Service_Getpassword();

            if ($service->isValidHash($this->getRequest()->getRequest('hash')))
            {
                $mail = new Base_Mail();
                $mail->setFrom(Base_Registry::getInstance()->config['robot_email_adress']);
                $mail->setReplyTo(Base_Registry::getInstance()->config['robot_email_adress']);
                $mail->setHeader('Ваш новый пароль на сайт '.$_SERVER['HTTP_HOST']);
                $mail->setTemplate($this->getTemplateFilePath('FrontendGetpasswordSendPassword'));

                $service->setMail($mail);

                $service->sendMailWithNewPassword();

                $redirect = new Base_Redirect();
                $redirect->setHeader('action_complete');
                $redirect->setMessage('getpassword_send_message');
                $redirect->setRedirectUrl('/my/');
                return $redirect->run();
            }
            else
            {
                $redirect = new Base_Redirect();
                $redirect->setType('warning');
                $redirect->setHeader('bad_hash_header');
                $redirect->setMessage('bad_hash_message');
                $this->getView()->setRedirect($redirect);
            }
        }
        catch (Exception $e)
        {
            $validator = new Validator_Chain('common/general');
            $validator->addError('common_error', 'SYSTEM_ERROR', array('error_message' => $e->getMessage()));
            $this->getView()->err = $validator->getErrors();
        }

        return $this->getView();
    }
}