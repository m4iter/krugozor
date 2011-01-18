<?php
class Module_Advert_Controller_FrontendActiveAdvert extends Module_Advert_Controller_FrontendCommon
{
    public function run()
    {
    	parent::common();

        if ($result = $this->checkIdOnValid())
        {
            return $result;
        }

        if (!$this->checkAccess() || $this->getCurrentUser()->getId() !== $this->advert->getIdUser())
        {
            $redirect = new Base_Redirect();
            $redirect->setMessage('forbidden_access');
            $redirect->setType('alert');
            $redirect->setRedirectUrl($this->getRequest()->getRequest('referrer') ?: '/my/');
            return $redirect->run();
        }

        self::getMapper('Advert/Advert')->save($this->advert->invertActive());

        $redirect = new Base_Redirect();
        $redirect->addParam('advert_header', Helper_Format::hsc($this->advert->getHeader()));
        $redirect->setMessage('advert_active_' . (string) $this->advert->getActive());
        $redirect->setRedirectUrl($this->getRequest()->getRequest('referrer') ?: '/my/adverts/');

        return $redirect->run();
    }
}