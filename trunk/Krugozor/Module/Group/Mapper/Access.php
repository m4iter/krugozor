<?php
class Module_Group_Mapper_Access extends Module_Common_Mapper_Common
{
    protected function init()
    {
        $this->db_table_name = 'group_access';
        $this->model_class_name = 'Module_Group_Model_Access';
    }

    /**
     * Возвращает коллекцию объектов доступа, найденных по ID группы.
     *
     * @param int ID группы
     * @return Cover_Array
     */
    public function findListByGroupId($id)
    {
        $params = array
        (
            'where' => array('id_group = ?i' => array($id))
        );

        return $this->findList($params);
    }

    /**
     * Удаляет все доступы группы $group.
     *
     * @param Module_Group_Model_Group $group
     * @return bool
     */
    public function clearByGroup(Module_Group_Model_Group $group)
    {
        return $this->db->query
        (
            'DELETE FROM `' . $this->db_table_name . '` WHERE `id_group` = ?i', $group->getId()
        );
    }

    /**
     * Сохраняет доступы группы $group.
     *
     * @param Base_Model $group
     * @return bool
     */
    public function saveAccesses(Base_Model $group)
    {
        if (!$group->getAccesses()->count())
        {
            return false;
        }
        
        $this->clearByGroup($group);

        $sql = 'REPLACE INTO `' . $this->db_table_name . '` (id_group, id_controller, access) VALUES ';

        foreach ($group->getAccesses() as $access)
        {
            $sql .= '(' . $group->getId() . ', ' .
                          $access->getIdController() . ', ' .
                          $access->getAccess() .
                    '), ';
        }

        $sql = rtrim($sql, ', ');

        return $this->db->query($sql);
    }

    /**
     * Возвращает многомерный массив, где индекс массива - ключ модуля
     * а значение - массив, индекс которого - ключ контроллера,
     * а значение - значение доступа группы $id_group к данному контроллеру - 1 или 0.
     * Пример:
     * Array
     * (
     *     [User] => Array
     *         (
     *             [BackendMain] => 1
     *             [BackendEdit] => 1
     *             [BackendDelete] => 1
     *             [FrontendEdit] => 1
     *     ...
     * )
     *
     * @param int $id_group
     * @return array 
     */
    public function getGroupAccessByIdWithControllerNames($id_group)
    {
        $res = $this->db->query('
             SELECT
                 `' . $this->db_table_name . '`.`access`,
                 `module`.`module_key`,
                 `module_controller`.`controller_key`
             FROM
                 `module`
             INNER JOIN
                 `module_controller`
             ON
                 `module`.`id` = `module_controller`.`controller_id_module`
             INNER JOIN
                 `' . $this->db_table_name . '`
             ON
                 `' . $this->db_table_name . '`.`id_controller` = `module_controller`.`id`
             INNER JOIN
                 `group`
             ON
                 `group`.`id` = `' . $this->db_table_name . '`.`id_group`
             WHERE
                 `group`.`id` = ?i', $id_group);

        $accesses = array();

        while ($data = $res->fetch_assoc())
        {
            if (!isset($accesses[$data['module_key']]))
            {
                $accesses[$data['module_key']] = array();
            }

            $accesses[$data['module_key']][$data['controller_key']] = $data['access'];
        }

        return $accesses;
    }
}