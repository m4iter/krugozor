<?php
class Module_Category_Controller_BackendMain extends Module_Category_Controller_BackendCommon
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            return $this->createNotification()
                        ->setMessage('forbidden_access')
                        ->setType('alert')
                        ->setRedirectUrl(array('admin'))
                        ->run();
        }

        $this->init();

        $this->getView()->categories = $this->getMapper('Category/Category')->loadTree(
            array('order' => array('order' => 'DESC'))
        );

        return $this->getView();
    }
}