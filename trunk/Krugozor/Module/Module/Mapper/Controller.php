<?php
class Module_Module_Mapper_Controller extends Module_Common_Mapper_Common
{
    protected function init()
    {
        $this->db_table_name = 'module_controller';
        $this->model_class_name = 'Module_Module_Model_Controller';
    }

    /**
     * ¬озвращает список контроллеров по ID модул€ $id.
     *
     * @param int $id
     * @return Cover_Array
     */
    public function findControllersListByModuleId($id)
    {
        $params = array
        (
            'where' => array('controller_id_module = ?i' => array($id)),
            'order' => array('controller_name' => 'ASC')
        );

        return $this->findList($params);
    }
}