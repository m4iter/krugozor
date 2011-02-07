<?php
class Module_Advert_Controller_FrontendUserAdvertsList extends Module_Advert_Controller_FrontendCommon
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            $redirect = new Base_Redirect($this->getDb());
            $redirect->setMessage('forbidden_access');
            $redirect->setType('alert');
            $redirect->setRedirectUrl(array('my'));
            return $redirect->run();
        }

        $this->init();

        $navigation = new Base_Navigation(10, 100);

        $this->getView()->adverts = $this->getMapper('Advert/Advert')->findListForUser($this->getCurrentUser()->getId(), $navigation->getStartLimit(), $navigation->getStopLimit());

        $navigation->setCount($this->getMapper('Advert/Advert')->getFoundRows());

        $this->getView()->navigation = $navigation;

        $this->getView()->current_user = $this->getCurrentUser();

        return $this->getView();
    }
}