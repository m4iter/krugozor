<?php
abstract class Module_ImageEmail_Controller_Common extends Module_Common_Controller_Common
{
    protected $view_bebug_info = FALSE;

    public function common()
    {
        $this->getResponse()->clearHeaders();

        parent::common();
    }
}
?>