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
            $redirect = new Base_Redirect($this->getDb());
            $redirect->setMessage('forbidden_access');
            $redirect->setType('alert');
            $redirect->setRedirectUrl($this->getRequest()->getRequest('referrer')
                                      ? $this->getRequest()->getRequest('referrer')
                                      : array('my'));
            return $redirect->run();
        }

        $this->getMapper('Advert/Advert')->deleteById($this->advert->getId());

        $redirect = new Base_Redirect($this->getDb());
        $redirect->setMessage('advert_delete');
        $redirect->addParam('advert_header', Helper_Format::hsc($this->advert->getHeader()));
        $redirect->setRedirectUrl($this->getRequest()->getRequest('referrer') ?: '/my/adverts/');
        return $redirect->run();
    }
}
?>