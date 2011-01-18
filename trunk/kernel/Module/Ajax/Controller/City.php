<?php
class Module_Ajax_Controller_City extends Module_Ajax_Controller_Common
{
    public function run()
    {
        parent::common();

        $this->getResponse()->setHeader('Content-type', 'text/plain; charset=windows-1251');

        $this->getView('JsonList');

        $this->getView()->jdata = self::getMapper('User/City')->getArrayListForHtmlSelect(
            $this->getRequest()->getRequest('id')
        );

        return $this->getView();
    }
}
?>