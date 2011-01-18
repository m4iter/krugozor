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
                $redirect = new Base_Redirect();
                $redirect->setType('alert');
                $redirect->setMessage('bad_id_element');
                $redirect->setRedirectUrl(array('admin', 'advert'));
                return $redirect->run();
            }

            $this->advert = self::getMapper('Advert/Advert')->findById(
                $this->getRequest()->getRequest('id')
            );

            if (!$this->advert->getId())
            {
                $redirect = new Base_Redirect();
                $redirect->setType('alert');
                $redirect->setMessage('element_does_not_exist');
                $redirect->setRedirectUrl(array('admin', 'advert'));
                return $redirect->run();
            }
        }
    }

    protected function init()
    {
        $this->getView()->loadI18n('Common/BackendGeneral', $this->getVirtualControllerPath());

        $this->getView()->getHelper('Html_Title')->add($this->getView()->lang['title']);
    }
}