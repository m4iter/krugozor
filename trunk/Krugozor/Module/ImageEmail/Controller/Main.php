<?php
class Module_ImageEmail_Controller_Main extends Module_ImageEmail_Controller_Common
{
    public function run()
    {
        parent::common();

        $this->getResponse()->setHeader('Content-type', 'image/png');

        if (!$this->getRequest()->getRequest('user_email_name') ||
            !$this->getRequest()->getRequest('user_email_domain'))
        {
            exit;
        }

        $imageEmail = new Module_ImageEmail_Model_ImageEmail(
            Base_Registry::getInstance()->path['font'].'Verdana.ttf'
        );

        $email = Module_Common_Type_Email::getEmailFromParts(
            $this->getRequest()->getRequest('user_email_name'),
            $this->getRequest()->getRequest('user_email_domain')
        );


        $imageEmail->setEmail($email)->create();

        return $imageEmail;
    }
}
?>