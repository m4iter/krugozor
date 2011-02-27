<?php
class Module_Advert_Controller_BackendDelete extends Module_Advert_Controller_BackendCommon
{
    public function run()
    {
        parent::common();

        if (!$this->checkAccess())
        {
            return $this->createNotification()
                        ->setMessage('forbidden_access')
                        ->setType('alert')
                        ->setRedirectUrl(array('admin', 'advert'))
                        ->run();
        }

        if ($result = $this->checkIdOnValid())
        {
            return $result;
        }

        if (empty($this->getRequest()->getRequest()->id))
        {
            return $this->createNotification()
                        ->setType('alert')
                        ->setMessage('id_element_not_exists')
                        ->setRedirectUrl(array('admin', 'advert'))
                        ->run();
        }

        $this->getMapper('Advert/Advert')->deleteById($this->advert);

        return $this->createNotification()
                    ->setMessage('element_delete')
                    ->setRedirectUrl($this->getRequest()->getRequest('referer'))
                    ->run();
    }
}