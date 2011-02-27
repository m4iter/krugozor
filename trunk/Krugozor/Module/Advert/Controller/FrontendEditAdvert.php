<?php
class Module_Advert_Controller_FrontendEditAdvert extends Module_Advert_Controller_FrontendCommon
{
    public function run()
    {
    	parent::common();

        if (!$this->checkAccess())
        {
            return $this->createNotification()
                        ->setMessage('forbidden_access')
                        ->setType('alert')
                        ->setRedirectUrl($this->getRequest()->getRequest('referrer') ?: array('my'))
                        ->run();
        }

        if ($result = $this->checkIdOnValid())
        {
            return $result;
        }

        $this->init();

        if (empty($this->advert))
        {
            $this->advert = $this->getMapper('Advert/Advert')->createNew($this->getCurrentUser());
        }

        if ($this->getCurrentUser()->getId() !== $this->advert->getIdUser() OR
            ($this->advert->getId() &&
             $this->getCurrentUser()->isGuest() &&
             $this->getCurrentUser()->getId() === $this->advert->getIdUser()
            )
           )
        {
            return $this->createNotification()
                        ->setMessage('forbidden_access')
                        ->setType('alert')
                        ->setRedirectUrl('/my/')
                        ->run();
        }

        if ($this->getCurrentUser()->isGuest() && 0)
        {
            $this->getView()->session_name = Base_Session::getInstance()->getName();
            $this->getView()->session_id = Base_Session::getInstance()->getId();
        }

        if (Http_Request::isPost() && ($result = $this->post()))
        {
            return $result;
        }

        $this->getView()->current_user = $this->getCurrentUser();
        $this->getView()->advert = $this->advert;
        $this->getView()->tree = $this->getMapper('Category/Category')->findCategoriesByActive(1);
        $this->getView()->from_registration = $this->getRequest()->getRequest()->from_registration;
        $this->getView()->advert_price_types = Module_Advert_Type_PriceType::getTypes();
        $this->getView()->advert_types = Module_Advert_Type_AdvertType::getTypes();

        return $this->getView();
    }

    protected function post()
    {
        $this->advert = $this->getMapper('Advert/Advert')->createFromCover($this->getRequest()->getPost('advert'),
            array('category', 'type', 'header', 'text', 'price', 'price_type', 'user_name', 'main_user_name',
            'phone', 'main_phone', 'email', 'main_email', 'url', 'main_url', 'icq', 'main_icq', 'place_country',
            'place_region', 'place_city', 'id')
        );

        $validator = new Validator_Chain('common/general', 'advert/edit');

        if ($this->getCurrentUser()->isGuest() &&
            !$this->advert->getIcq() && !$this->advert->getPhone() &&
            !$this->advert->getEmail() && !$this->advert->getUrl()
            OR
            !$this->getCurrentUser()->isGuest() &&
            !$this->advert->getIcq() && !$this->advert->getPhone() &&
            !$this->advert->getEmail() && !$this->advert->getUrl() &&
            !$this->advert->getMainIcq() && !$this->advert->getMainPhone() &&
            !$this->advert->getMainEmail() && !$this->advert->getMainUrl()
           )
        {
            $validator->addError('contact_info', 'EMPTY_CONTACT_INFO');
        }

        $validator->addModelErrors($this->advert->getValidateErrors());

        if ($this->getCurrentUser()->isGuest() && 0)
        {
            $validator->add('captcha', new Module_Common_Validator_Captcha(
                $this->getRequest()->getPost('captcha_code'), Base_Session::getInstance()->code
            ));
        }

        $validator->validate();

        if ($this->getView()->err = $validator->getErrors())
        {
            $redirect = $this->createNotification()
                             ->setType('alert')
                             ->setMessage('post_errors');
            $this->getView()->setRedirect($redirect);
        }
        else
        {
            if ($this->getCurrentUser()->isGuest() && 0)
            {
                Base_Session::getInstance($this->getView()->session_name)->destroy();
            }

            $this->advert->setIdUser($this->getCurrentUser()->getId());
            $this->advert->setActive(1);

            if ($this->advert->getId())
            {
                $this->advert->setEditDate(new Module_Common_Type_Datetime());
            }
            else
            {
                $this->advert->setCurrentCreateDateDiffSecond();
            }

            $this->getMapper('Advert/Advert')->save($this->advert);

            $category = $this->getMapper('Category/Category')->findById($this->advert->getCategory());

            return $this->createNotification()
                        ->setHeader('action_complete')
                        ->setMessage('advert_save_ok')
                        ->addParam('id', $this->advert->getId())
                        ->addParam('category_url', $category->getUrl())
                        ->addParam('advert_header', Helper_Format::hsc($this->advert->getHeader()))
                        ->setRedirectUrl($this->getCurrentUser()->isGuest()
                                         ? '/add.xhtml'
                                         : ($this->getRequest()->getRequest('referrer', 'string') ?: '/my/adverts/'))

                        ->run();
        }
    }
}