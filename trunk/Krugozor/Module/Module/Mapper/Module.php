<?php
class Module_Module_Mapper_Module extends Module_Common_Mapper_Common
{
    protected function init()
    {
        $this->db_table_name = 'module';
        $this->model_class_name = 'Module_Module_Model_Module';
    }

    /**
     * ¬озвращает массив объектов-модулей с загруженными в них контроллерами.
     * —ложный и неоправданный метод, существующий лишь по историческим причинам.
     *
     * @param array параметры поиска (только параметр order)
     * @return array
     */
    public function findModulesWithControllers(array $params=array())
    {
        $params = self::makeSqlFromParams($params);

        $data = array();

        $sql = 'SELECT
                    `' . $this->getTableName() . '`.id AS id_module,
                    `' . $this->getTableName() . '`.module_name,
                    `' . $this->getTableName() . '`.module_key,
                    `' . $this->manager->getMapper('Module/Controller')->getTableName() . '`.id AS id_controller,
                    `' . $this->manager->getMapper('Module/Controller')->getTableName() . '`.controller_id_module,
                    `' . $this->manager->getMapper('Module/Controller')->getTableName() . '`.controller_name,
                    `' . $this->manager->getMapper('Module/Controller')->getTableName() . '`.controller_key
                FROM
                    `' . $this->getTableName() . '`
                LEFT JOIN
                    `' . $this->manager->getMapper('Module/Controller')->getTableName() . '`
                ON
                    `' . $this->getTableName() . '`.`id` = `' .
                         $this->manager->getMapper('Module/Controller')->getTableName() .
                         '`.`controller_id_module`' . $params['order'];

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
                $controller_row = array
                (
                    'id' => $row['id_controller'],
                    'controller_id_module' => $row['controller_id_module'],
                    'controller_name' => $row['controller_name'],
                    'controller_key' => $row['controller_key']
                );

                $data[$row['id_module']]->setController(
                    $this->manager->getMapper('Module/Controller')->createModelFromArray($controller_row)
                );
            }
        }

        return $data;
    }

    public function delete($object)
    {
        parent::deleteById($object);

        $params = array
        (
            'where'=> array('`controller_id_module` = ?i' => array($object->getId()))
        );

        $this->manager->getMapper('Module/Controller')->deleteByParams($params);
    }
}