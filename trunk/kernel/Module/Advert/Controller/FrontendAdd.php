<?php
class Module_Advert_Controller_FrontendAdd extends Module_Advert_Controller_FrontendCommon
{
    public function run()
    {
        parent::common();

        $this->getView()->current_user = $this->getCurrentUser();

        $this->init();

        return $this->getView();
    }
}
?>