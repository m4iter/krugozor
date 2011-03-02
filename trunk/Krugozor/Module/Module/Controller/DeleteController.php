<?php
class Module_Module_Controller_DeleteController extends Module_Module_Controller_CommonController
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            return $this->createNotification()
                        ->setMessage('forbidden_access')
                        ->setType('alert')
                        ->setRedirectUrl(Base_Redirect::implode('admin','module', 'edit') .
                                         '?id=' . $this->getRequest()->getRequest('id_module'))
                        ->run();
        }

        if ($result = $this->checkIdOnValid())
        {
            return $result;
        }

        $this->getMapper('Module/Controller')->deleteById($this->controller);

        return $this->createNotification()
                    ->setMessage('element_delete')
                    ->setRedirectUrl(Base_Redirect::implode('admin', 'module', 'edit') .
                                     '?id=' . $this->getRequest()->getRequest('id_module'))
                    ->run();
    }
}