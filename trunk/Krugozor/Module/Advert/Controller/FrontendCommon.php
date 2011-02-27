<?php
abstract class Module_Advert_Controller_FrontendCommon extends Module_Common_Controller_Common
{
    protected $advert;

    protected function checkIdOnValid()
    {
        if ($id = $this->getRequest()->getRequest('id'))
        {
            if (!Base_Numeric::is_decimal($id))
            {
                return $this->createNotification()
                            ->setType('alert')
                            ->setMessage('bad_id_advert')
                            ->setRedirectUrl(array('my', 'adverts'))
                            ->run();
            }

            $this->advert = $this->getMapper('Advert/Advert')->findById($this->getRequest()->getRequest('id'));

            if (!$this->advert->getId())
            {
                return $this->createNotification()
                            ->setType('alert')
                            ->setMessage('advert_does_not_exist')
                            ->setRedirectUrl(array('my', 'adverts'))
                            ->run();
            }
        }

        return null;
    }

    public function init()
    {
        $this->getView()->loadI18n('Common/FrontendGeneral', $this->getVirtualControllerPath());

        $this->getView()->getHelper('Html_Title')->add($this->getView()->lang['title']);
    }
}