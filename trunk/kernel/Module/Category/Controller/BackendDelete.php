<?php
class Module_Category_Controller_BackendDelete extends Module_Category_Controller_BackendCommon
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            $redirect = new Base_Redirect();
            $redirect->setMessage('forbidden_access');
            $redirect->setType('alert');
            $redirect->setRedirectUrl(array('admin', 'category'));
            return $redirect->run();
        }

        if ($result = $this->checkIdOnValid())
        {
            return $result;
        }

        if (!isset($this->getRequest()->getRequest()->id))
        {
            $redirect = new Base_Redirect();
            $redirect->setType('alert');
            $redirect->setMessage('element_not_exists');
            $redirect->setRedirectUrl(array('admin', 'category'));
            return $redirect->run();
        }

        self::getMapper('Category/Category')->deleteById($this->category);

        $redirect = new Base_Redirect();
        $redirect->setMessage('element_delete');
        $redirect->setRedirectUrl($this->getRequest()->getRequest('referer'));
        return $redirect->run();
    }
}