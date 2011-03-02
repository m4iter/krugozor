<?php
class Module_Category_Controller_BackendEdit extends Module_Category_Controller_BackendCommon
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            return $this->createNotification()
                        ->setMessage('forbidden_access')
                        ->setType('alert')
                        ->setRedirectUrl(array('admin', 'category'))
                        ->run();
        }

        if ($result = $this->checkIdOnValid())
        {
            return $result;
        }

        $this->init();

        if (empty($this->category))
        {
            $this->category = $this->getMapper('Category/Category')->createNew();

            $this->category->setPid(
                $this->getRequest()->getRequest('pid', 'decimal')
            );
        }

        if (Http_Request::isPost() && ($result = $this->post()))
        {
            return $result;
        }

        $this->getView()->category = $this->category;

        $this->getView()->tree = $this->getMapper('Category/Category')->findCategoriesByActive();

        $this->getView()->return_on_page = $this->getRequest()->getRequest('return_on_page');

        return $this->getView();
    }

    protected function post()
    {
        $this->category = $this->getMapper('Category/Category')->createFromCover($this->getRequest()->getPost('category'));

        if (!$this->category->getAlias())
        {
            $this->category->setAlias($this->category->getName());
        }

        $validator = new Validator_Chain('common/general');

        $validator->addModelErrors($this->category->getValidateErrors());

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
            $this->getMapper('Category/Category')->save($this->category);

            return $this->createNotification()
                        ->setMessage('element_edit_ok')
                        ->setRedirectUrl($this->getRequest()->getRequest('return_on_page')
	                                      ? Base_Redirect::implode('admin',
	                                                               'category',
	                                                               'edit',
	                                                               $this->category->getId()
	                                                              )
	                                      : (
	                                            $this->getRequest()->getRequest('referer')
	                                            ? $this->getRequest()->getRequest('referer')
	                                            : Base_Redirect::implode('admin', 'category')
	                                        )
	                                     )

                        ->run();
        }
    }
}
?>