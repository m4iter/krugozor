<?php
abstract class Module_Category_Controller_BackendCommon extends Module_Common_Controller_Common
{
    protected $category;

    protected function checkIdOnValid()
    {
        if ($id = $this->getRequest()->getRequest('id'))
        {
            if (!Base_Numeric::is_decimal($id))
            {
                $redirect = new Base_Redirect($this->getDb());
                $redirect->setType('alert');
                $redirect->setMessage('bad_id_element');
                $redirect->setRedirectUrl(array('admin', 'category'));
                return $redirect->run();
            }

            $this->category = $this->getMapper('Category/Category')->findById(
                $this->getRequest()->getRequest('id')
            );

            if (!$this->category->getId())
            {
                $redirect = new Base_Redirect($this->getDb());
                $redirect->setType('alert');
                $redirect->setMessage('element_does_not_exist');
                $redirect->setRedirectUrl(array('admin', 'category'));
                return $redirect->run();
            }
        }
    }

    protected function init()
    {
        $this->getView()->loadI18n('Common/BackendGeneral', $this->getVirtualControllerPath());

        $this->getView()->getHelper('Html_Title')->add($this->getView()->lang['title']);
    }
}