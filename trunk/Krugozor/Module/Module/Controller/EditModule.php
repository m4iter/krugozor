<?php
class Module_Module_Controller_EditModule extends Module_Module_Controller_CommonModule
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            $redirect = new Base_Redirect($this->getDb());
            $redirect->setMessage('forbidden_access');
            $redirect->setType('alert');
            $redirect->setRedirectUrl(array('admin', 'module'));
            return $redirect->run();
        }

        if ($result = $this->checkIdOnValid())
        {
            return $result;
        }

        $this->init();

        if (empty($this->module))
        {
            $this->module = $this->getMapper('Module/Module')->createNew();
        }

        if (Http_Request::isPost() && ($result = $this->post()))
        {
            return $result;
        }

        $this->getView()->module = $this->module;

        return $this->getView();
    }

    protected function post()
    {
        $this->module = $this->getMapper('Module/Module')->createFromCover(
            $this->getRequest()->getPost('module')
        );

        $validator = new Validator_Chain('common/general', 'module/editModule');

        $validator->addModelErrors($this->module->getValidateErrors());

        if ($this->module->getName())
        {
            $validator->add('name', new Module_Module_Validator_ModuleNameExists(
                $this->module, $this->getMapper('Module/Module')
            ));
        }

        if ($this->module->getKey())
        {
            $validator->add('key', new Module_Module_Validator_ModuleKeyExists(
                $this->module, $this->getMapper('Module/Module')
            ));
        }

        $validator->validate();

        if ($this->getView()->err = $validator->getErrors())
        {
            $redirect = new Base_Redirect($this->getDb());
            $redirect->setType('alert');
            $redirect->setMessage('post_errors');
            $this->getView()->setRedirect($redirect);

            $this->getMapper('Module/Module')->loadControllers($this->module);
        }
        else
        {
            $this->getMapper('Module/Module')->save($this->module);

            $redirect = new Base_Redirect($this->getDb());
            $redirect->setMessage('element_edit_ok');
            $redirect->setRedirectUrl($this->getRequest()->getRequest('return_on_page')
                                      ? Base_Redirect::implode('admin', 'module', 'edit').
                                      '?id='.$this->module->id
                                      : (
                                            $this->getRequest()->getRequest()->referer
                                            ? $this->getRequest()->getRequest()->referer
                                            : array('admin', 'module')
                                        )
                                     );
            return $redirect->run();
        }
    }
}
?>