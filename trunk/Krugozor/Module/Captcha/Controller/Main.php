<?php
class Module_Captcha_Controller_Main extends Module_Captcha_Controller_Common
{
    public function run()
    {
        parent::common();

        $this->getResponse()->setHeader('Content-type', 'image/png');

        if (!$this->getRequest()->getRequest('CAPTCHASID'))
        {
            exit;
        }

        $Base_Session = Base_Session::getInstance('CAPTCHASID');

        $captcha = new Module_Captcha_Model_Captcha(
            Base_Registry::getInstance()->path['font'].'ASmperCmUp.ttf'
        );

		$Base_Session->code = $captcha->getCode();

		$captcha->create();

		return $captcha;
    }
}
?>