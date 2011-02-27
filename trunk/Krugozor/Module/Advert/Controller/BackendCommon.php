<?php
abstract class Module_Advert_Controller_BackendCommon extends Module_Common_Controller_Common
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
                            ->setMessage('bad_id_element')
                            ->setRedirectUrl(array('admin', 'advert'))
                            ->run();
            }

            $this->advert = $this->getMapper('Advert/Advert')->findById(
                $this->getRequest()->getRequest('id')
            );

            if (!$this->advert->getId())
            {
                return $this->createNotification()
                            ->setType('alert')
                            ->setMessage('element_does_not_exist')
                            ->setRedirectUrl(array('admin', 'advert'))
                            ->run();
            }
        }
    }

    protected function init()
    {
        $this->getView()->loadI18n('Common/BackendGeneral', $this->getVirtualControllerPath());

        $this->getView()->getHelper('Html_Title')->add($this->getView()->lang['title']);
    }
}