<?php
class Module_Advert_Controller_FrontendUpAdvert extends Module_Advert_Controller_FrontendCommon
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

        $redirect = new Base_Redirect($this->getDb());
        $redirect->addParam('advert_header', Helper_Format::hsc($this->advert->getHeader()));

        if ($this->getMapper('Advert/Advert')->updateDateCreate($this->advert))
        {
            $redirect->setMessage('advert_date_create_update');
        }
        else
        {
            $redirect->setType('warning');
            $redirect->addParam('date', $this->advert->getExpireRestrictionUpdateCreateDate()->i);
            $redirect->setMessage('advert_date_create_not_update');
        }

        $redirect->setRedirectUrl($this->getRequest()->getRequest('referrer') ?: '/my/adverts/');

        return $redirect->run();
    }
}
?>