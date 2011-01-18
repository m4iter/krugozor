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
                $redirect = new Base_Redirect();
                $redirect->setType('alert');
                $redirect->setMessage('bad_id_advert');
                $redirect->setRedirectUrl(array('my', 'adverts'));
                return $redirect->run();
            }

            $this->advert = self::getMapper('Advert/Advert')->findById($this->getRequest()->getRequest('id'));

            if (!$this->advert->getId())
            {
                $redirect = new Base_Redirect();
                $redirect->setType('alert');
                $redirect->setMessage('advert_does_not_exist');
                $redirect->setRedirectUrl(array('my', 'adverts'));
                return $redirect->run();
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