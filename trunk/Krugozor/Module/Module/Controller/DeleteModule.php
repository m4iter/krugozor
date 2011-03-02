<?php
class Module_Module_Controller_DeleteModule extends Module_Module_Controller_CommonModule
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            return $this->createNotification()
                        ->setMessage('forbidden_access')
                        ->setType('alert')
                        ->setRedirectUrl(array('admin','module'))
                        ->run();
        }

        if ($result = $this->checkIdOnValid())
        {
            return $result;
        }

        if (!isset($this->getRequest()->getRequest()->id))
        {
            return $this->createNotification()
                        ->setType('alert')
                        ->setMessage('id_element_not_exists')
                        ->setRedirectUrl(array('admin', 'module'))
                        ->run();
        }

        $this->getMapper('Module/Module')->delete($this->module);

        return $this->createNotification()
                    ->setMessage('element_delete')
                    ->setRedirectUrl($this->getRequest()->getRequest()->referer)
                    ->run();
    }
}