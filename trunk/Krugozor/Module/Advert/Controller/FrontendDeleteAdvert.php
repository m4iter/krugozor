<?php
class Module_Advert_Controller_FrontendDeleteAdvert extends Module_Advert_Controller_FrontendCommon
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
                        ->setRedirectUrl($this->getRequest()->getRequest('referrer')
                                      ? $this->getRequest()->getRequest('referrer')
                                      : array('my'))
                        ->run();
        }

        $this->getMapper('Advert/Advert')->deleteById($this->advert->getId());

        return $this->createNotification()
                    ->setMessage('advert_delete')
                    ->addParam('advert_header', Helper_Format::hsc($this->advert->getHeader()))
                    ->setRedirectUrl($this->getRequest()->getRequest('referrer') ?: '/my/adverts/')
                    ->run();
    }
}