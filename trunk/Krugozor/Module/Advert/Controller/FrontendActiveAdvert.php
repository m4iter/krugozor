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
            return $this->createNotification()
                        ->setMessage('forbidden_access')
                        ->setType('alert')
                        ->setRedirectUrl($this->getRequest()->getRequest('referrer') ?: '/my/')
                        ->run();
        }

        $this->getMapper('Advert/Advert')->save($this->advert->invertActive());

        return $this->createNotification()
                    ->addParam('advert_header', Helper_Format::hsc($this->advert->getHeader()))
                    ->setMessage('advert_active_' . (string) $this->advert->getActive())
                    ->setRedirectUrl($this->getRequest()->getRequest('referrer') ?: '/my/adverts/')
                    ->run();
    }
}