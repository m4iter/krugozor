<?php
class Module_Module_Mapper_Module extends Module_Common_Mapper_Common
{
    public function __construct()
    {
        parent::__construct();

        $this->db_table_name = 'module';

        $this->model_class_name = 'Module_Module_Model_Module';
    }

    /**
     * Возвращает массив объектов-модулей с контроллерами.
     *
     * @access public
     * @param array параметры поиска (только параметр order)
     * @return array
     */
    public function loadModulesWithControllers(array $params=array())
    {
        $params = self::makeSqlFromParams($params);

        $data = array();

        $Module_Module_Mapper_Controller = new Module_Module_Mapper_Controller();

        $sql = 'SELECT
                    `'.$this->db_table_name.'`.id AS id_module,
                    `'.$this->db_table_name.'`.module_name,
                    `'.$this->db_table_name.'`.module_key,
                    `'.$Module_Module_Mapper_Controller->getTableName().'`.id AS id_controller,
                    `'.$Module_Module_Mapper_Controller->getTableName().'`.controller_id_module,
                    `'.$Module_Module_Mapper_Controller->getTableName().'`.controller_name,
                    `'.$Module_Module_Mapper_Controller->getTableName().'`.controller_key
                    FROM
                    `'.$this->db_table_name.'`
                LEFT JOIN
                    `'.$Module_Module_Mapper_Controller->getTableName().'`
                ON
                    `'.$this->db_table_name.'`.`id` = `'.$Module_Module_Mapper_Controller->getTableName().'`.`controller_id_module`
                '.
                $params['order'];
                //.$params['limit'];

        $res = $this->db->query($sql);

        while ($row = $res->fetch_assoc())
        {
            if (!isset($data[$row['id_module']]))
            {
                $data[$row['id_module']] = $this->createNew();
                $data[$row['id_module']]->setId($row['id_module']);
                $data[$row['id_module']]->setName($row['module_name']);
                $data[$row['id_module']]->setKey($row['module_key']);
            }

            if ($row['controller_id_module'])
            {
                $controller_row = array('id' => $row['id_controller'],
                                        'controller_id_module' => $row['controller_id_module'],
                                        'controller_name' => $row['controller_name'],
                                        'controller_key' => $row['controller_key']);

                $data[$row['id_module']]->getControllers()->append( $Module_Module_Mapper_Controller->createModelFromArray($controller_row) );
            }
        }

        return $data;
    }

    public function delete($object)
    {
        parent::deleteById($object);

        $module_mapper = new Module_Module_Mapper_Controller();
        $params = array
        (
            'where'=> array('`controller_id_module` = ?i' => array($object->getId()))
        );
        $module_mapper->deleteByParams($params);
    }
}
?>