<?php
class Module_Module_Controller_EditModule extends Module_Module_Controller_CommonModule
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            return $this->createNotification()
                        ->setMessage('forbidden_access')
                        ->setType('alert')
                        ->setRedirectUrl(array('admin', 'module'))
                        ->run();
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
            $redirect = $this->createNotification()
                             ->setType('alert')
                             ->setMessage('post_errors');
            $this->getView()->setRedirect($redirect);
        }
        else
        {
            $this->getMapper('Module/Module')->save($this->module);

            return $this->createNotification()
                        ->setMessage('element_edit_ok')
                        ->setRedirectUrl($this->getRequest()->getRequest('return_on_page')
                                          ? Base_Redirect::implode('admin', 'module', 'edit').
                                          '?id='.$this->module->id
                                          : (
                                                $this->getRequest()->getRequest()->referer
                                                ? $this->getRequest()->getRequest()->referer
                                                : array('admin', 'module')
                                            )
                                         )
                        ->run();
        }
    }
}