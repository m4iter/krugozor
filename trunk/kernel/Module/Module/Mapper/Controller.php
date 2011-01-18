<?php
class Module_Module_Mapper_Controller extends Module_Common_Mapper_Common
{
    public function __construct()
    {
        parent::__construct();
        $this->db_table_name = 'controller';
        $this->model_class_name = 'Module_Module_Model_Controller';
    }
}
?>