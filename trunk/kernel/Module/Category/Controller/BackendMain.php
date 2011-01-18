<?php
class Module_Category_Controller_BackendMain extends Module_Category_Controller_BackendCommon
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            $redirect = new Base_Redirect();
            $redirect->setMessage('forbidden_access');
            $redirect->setType('alert');
            $redirect->setRedirectUrl(array('admin'));
            return $redirect->run();
        }

        $this->init();

        $this->getView()->categories = self::getMapper('Category/Category')->loadTree(
            array('order' => array('order' => 'DESC'))
        );

        return $this->getView();
    }
}
?>