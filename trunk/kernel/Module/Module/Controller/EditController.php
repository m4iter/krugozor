<?php
class Module_Module_Controller_EditController extends Module_Module_Controller_CommonController
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            $redirect = new Base_Redirect();
            $redirect->setMessage('forbidden_access');
            $redirect->setType('alert');
            $redirect->setRedirectUrl(Base_Redirect::implode('admin', 'module', 'edit').
                                      '?id='.$this->getRequest()->getRequest('id_module'));
            return $redirect->run();
        }

        if ($result = $this->checkIdOnValid())
        {
            return $result;
        }

        $this->init();

        if (empty($this->controller))
        {
            $this->controller = self::getMapper('Module/Controller')->createNew();
            $this->controller->setIdModule($this->getRequest()->getRequest('id_module'));
        }

        if (Http_Request::isPost() && ($result = $this->post()))
        {
            return $result;
        }

        $this->getView()->controller = $this->controller;

        $this->getView()->modules = self::getMapper('Module/Module')->findList();

        return $this->getView();
    }

    protected function post()
    {
        $this->controller = self::getMapper('Module/Controller')->createFromCover(
            $this->getRequest()->getPost('controller')
        );

        $validator = new Validator_Chain('common/general');

        $validator->addModelErrors($this->controller->getValidateErrors());

        $validator->validate();

        if ($this->getView()->err = $validator->getErrors())
        {
            $redirect = new Base_Redirect();
            $redirect->setType('alert');
            $redirect->setMessage('post_errors');
            $this->getView()->setRedirect($redirect);
        }
        else
        {
            self::getMapper('Module/Controller')->save($this->controller);

            $redirect = new Base_Redirect();
            $redirect->setMessage('element_edit_ok');
            $redirect->setRedirectUrl($this->getRequest()->getRequest('return_on_page')
                                      ? Base_Redirect::implode('admin','controller', 'edit',
                                                                   $this->controller->getId(),
                                                                   $this->controller->getIdModule()
                                                                  )
                                      : (
                                            $this->getRequest()->getRequest('referer')
                                            ? $this->getRequest()->getRequest('referer')
                                            : Base_Redirect::implode('admin', 'module', 'edit').
                                              '?id='.$this->getRequest()->getRequest('id_module')
                                        )
                                     );
            return $redirect->run();
        }
    }
}
?>