<?php
class Module_Advert_Controller_FrontendUserAdvertsList extends Module_Advert_Controller_FrontendCommon
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            return $this->createNotification()
                        ->setMessage('forbidden_access')
                        ->setType('alert')
                        ->setRedirectUrl(array('my'))
                        ->run();
        }

        $this->init();

        $navigation = new Base_Navigation(10, 100);

        $this->getView()->adverts = $this->getMapper('Advert/Advert')->findListForUser(
            $this->getCurrentUser()->getId(), $navigation->getStartLimit(), $navigation->getStopLimit()
        );

        $navigation->setCount($this->getMapper('Advert/Advert')->getFoundRows());

        $this->getView()->navigation = $navigation;

        $this->getView()->current_user = $this->getCurrentUser();

        return $this->getView();
    }
}