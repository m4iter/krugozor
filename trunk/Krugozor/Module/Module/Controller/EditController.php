<?php
class Module_Module_Controller_EditController extends Module_Module_Controller_CommonController
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            $redirect = new Base_Redirect($this->getDb());
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
            $this->controller = $this->getMapper('Module/Controller')
                                     ->createNew()
                                     ->setIdModule($this->getRequest()->getRequest('id_module'));
        }

        if (Http_Request::isPost() && ($result = $this->post()))
        {
            return $result;
        }

        $this->getView()->modules = $this->getMapper('Module/Module')->findList();
        $this->getView()->controller = $this->controller;

        return $this->getView();
    }

    protected function post()
    {
        $this->controller = $this->getMapper('Module/Controller')->createFromCover(
            $this->getRequest()->getPost('controller')
        );

        $validator = new Validator_Chain('common/general');
        $validator->addModelErrors($this->controller->getValidateErrors())
                  ->validate();

        $redirect = new Base_Redirect($this->getDb());

        if ($this->getView()->err = $validator->getErrors())
        {
            $redirect->setType('alert');
            $redirect->setMessage('post_errors');
            $this->getView()->setRedirect($redirect);
        }
        else
        {
            $this->getMapper('Module/Controller')->save($this->controller);

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