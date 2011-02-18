<?php
class Module_Advert_Controller_BackendEdit extends Module_Advert_Controller_BackendCommon
{
    private $user;

    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            $redirect = new Base_Redirect($this->getDb());
            $redirect->setMessage('forbidden_access');
            $redirect->setType('alert');
            $redirect->setRedirectUrl(array('admin', 'advert'));
            return $redirect->run();
        }

        $this->init();

        if ($result = $this->checkIdOnValid())
        {
            return $result;
        }

        if (empty($this->advert))
        {
            $this->advert = $this->getMapper('Advert/Advert')->createNew(
                $this->getMapper('User/User')->findById(-1)
            );
        }

        $this->user = $this->getMapper('User/User')->findById($this->advert->getIdUser());

        if (Http_Request::isPost() && ($result = $this->post()))
        {
            return $result;
        }

        $this->getView()->tree = $this->getMapper('Category/Category')->findCategoriesByActive(1);
        $this->getView()->advert = $this->advert;
        $this->getView()->user = $this->user;
        $this->getView()->return_on_page = $this->getRequest()->getRequest('return_on_page');
        $this->getView()->advert_price_types = Module_Advert_Type_PriceType::getTypes();
        $this->getView()->advert_types = Module_Advert_Type_AdvertType::getTypes();

        return $this->getView();
    }

    protected function post()
    {
        $this->advert = $this->getMapper('Advert/Advert')->createFromCover(
            $this->getRequest()->getPost('advert'), array(
                'category', 'id_user', 'type', 'header', 'text', 'price',
                'user_name', 'main_user_name', 'phone', 'main_phone', 'email',
                'main_email', 'url', 'main_url', 'icq', 'main_icq',
                'place_country', 'place_region', 'place_city', 'id'));

        $validator = new Validator_Chain('common/general', 'advert/edit', 'user/common');

        if ($this->user->isGuest() AND
            !$this->advert->getIcq() && !$this->advert->getPhone() &&
            !$this->advert->getEmail() && !$this->advert->getUrl())
        {
            $validator->addError('contact_info', 'EMPTY_CONTACT_INFO');
        }

        $validator->addModelErrors($this->advert->getValidateErrors());

        if (!$this->advert->getValidateErrorsByKey('id_user'))
        {
            $validator->add('id_user', new Module_User_Validator_UserIdExists(
                $this->advert->getIdUser(), $this->getMapper('User/User')
            ));
        }

        $validator->validate();

        $redirect = new Base_Redirect($this->getDb());

        if ($this->getView()->err = $validator->getErrors())
        {
            $redirect->setType('alert');
            $redirect->setMessage('post_errors');
            $this->getView()->setRedirect($redirect);
        }
        else
        {
            if (!$this->advert->getId())
            {
                $this->advert->setCurrentCreateDateDiffSecond();
            }

            $this->getMapper('Advert/Advert')->save($this->advert);

            $category = $this->getMapper('Category/Category')->findById(
                $this->advert->getCategory()
            );

            $redirect->setHeader('action_complete');
            $redirect->setMessage('element_edit_ok');
            $redirect->setRedirectUrl($this->getRequest()->getRequest('return_on_page')
                                      ? Base_Redirect::implode('admin', 'advert', 'edit') .
                                        '?id='.$this->advert->getId()
                                      : (
                                            $this->getRequest()->getRequest('referer')
                                            ?: Base_Redirect::implode('admin', 'advert')
                                        )
                                     );
            $this->getView()->setRedirect($redirect);
            return $redirect->run();
        }
    }
}