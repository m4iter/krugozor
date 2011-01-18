<?php
class Module_Ajax_Controller_AdvertPhone extends Module_Ajax_Controller_Common
{
    public function run()
    {
        parent::common();

        $this->getResponse()->setHeader('Content-type', 'text/plain; charset=windows-1251');

        $this->getView('JsonSimple');

        $advert = self::getMapper('Advert/Advert')->findByParams(
            array('what' => 'id, advert_id_user, advert_phone',
                  'where' => array('id = ?i' => array($this->getRequest()->getRequest('id')))
                 )
        );

        $phone = $advert->getPhone();

        if (!$phone && $advert->getId() > 0)
        {
            $user = self::getMapper('User/User')->findByParams(
                array('what' => 'user_phone', 'where' => array('id = ?i' => array($advert->getIdUser())))
            );

            $phone = $user->getPhone();
        }

        $this->getView()->jdata = array('key' => 'phone',
                                        'value' => $phone);

        return $this->getView();
    }
}