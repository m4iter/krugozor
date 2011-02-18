<?php
class Module_Ajax_Controller_Country extends Module_Ajax_Controller_Common
{
    public function run()
    {
        parent::common();

        $this->getResponse()->setHeader('Content-type', 'text/plain; charset=windows-1251');

        $this->getView('JsonList');

        $this->getView()->jdata = $this->getMapper('User/Country')->getArrayListForHtmlSelect();

        return $this->getView();
    }
}
?>