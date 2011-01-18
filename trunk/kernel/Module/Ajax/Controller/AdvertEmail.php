<?php
class Module_Ajax_Controller_AdvertEmail extends Module_Ajax_Controller_Common
{
    public function run()
    {
        parent::common();

        $this->getResponse()->setHeader('Content-type', 'text/plain; charset=windows-1251');

        $this->getView('JsonSimple');

        $advert = self::getMapper('Advert/Advert')->findByParams(
            array('what' => 'id, advert_id_user, advert_email',
                  'where' => array('id = ?i' => array($this->getRequest()->getRequest('id')))
                 )
        );

        $email = $advert->getEmail();

        if (!$email && $advert->getId() > 0)
        {
            $user = self::getMapper('User/User')->findByParams(
                array('what' => 'user_mail', 'where' => array('id = ?i' => array($advert->getIdUser())))
            );

            $email = $user->getMail()->getValue();
        }

        $this->getView()->jdata = array('key' => 'email',
                                        'value' => $email);

        return $this->getView();
    }
}