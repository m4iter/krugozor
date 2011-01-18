<?php
class Base_Access
{
    /**
     * instance объекта Базы Данных.
     *
     * @var Db_Mysql_Base
     * @access private
     */
    private $db;

    public function __construct()
    {
        $this->db = Base_Registry::getInstance()->objects['db'];
    }

    /**
     * Очищает таблицу group_action по
     * идентификатору группы $id.
     *
     * @param int $id
     */
    public function clearGroupRulesById($id)
    {
        $this->db->query('DELETE FROM `group_action` WHERE `id_group` = ?i', $id);
    }

    /**
     * Сохраняет многомерный массив вида
     * array(1 => array(1=>1, 2=>0, 3=>0))
     *  где 1 - идентификатор модуля, а массив
     *  array(1=>1, 2=>0, 3=>0) - массив соответствий
     *  id_контроллера => право
     *
     * @param unknown_type $id
     * @param unknown_type $group_access
     */
    public function saverGroupRulesById($id, $group_access)
    {
        $sql = 'REPLACE INTO `group_action` (id_group, id_controller, access) VALUES ';

        foreach ($group_access as $id_module => $controllers)
        {
            foreach ($controllers as $id_controller => $access)
            {
                $sql .= '('.$id.', '.$id_controller.', '.$access.'), ';
            }
        }

        $sql = rtrim($sql, ', ');

        return $this->db->query($sql);
    }

    /**
     * Возвращает многомерный массив вида
     * array(1 => array(1=>1, 2=>0, 3=>0))
     *  где 1 - идентификатор модуля, амассив
     *  array(1=>1, 2=>0, 3=>0) - массив соответствий
     *  id_контроллера => право
     *
     * @param int $id
     * @return array
     */
    public function getGroupRulesById($id)
    {
        $group_actions = array();

        $res = $this->db->query('SELECT
                                     module.id AS id_module,
                                     controller.id AS id_controller,
                                     IFNULL(group_action.access, 0) as access
                                 FROM
                                     module
                                 INNER JOIN
                                     controller
                                 ON
                                     module.id = controller.controller_id_module
                                 LEFT JOIN
                                     group_action
                                 ON
                                     group_action.id_controller = controller.id
                                 AND
                                     group_action.id_group = ?i
                                ', $id);

        while ($row = $res->fetch_assoc())
        {
            if (!isset($group_actions[$row['id_module']]))
            {
                $group_actions[$row['id_module']] = array();
            }

            $group_actions[$row['id_module']][$row['id_controller']] = $row['access'];
        }

        return $group_actions;
    }

    public function getGroupRulesByIdWithControllerNames($id_group)
    {
        $res = $this->db->query('
                 SELECT
                     `group_action`.`access`,
                     `module`.`module_key`,
                     `controller`.`controller_key`
                 FROM
                     `module`
                 INNER JOIN
                     `controller`
                 ON
                     `module`.`id` = `controller`.`controller_id_module`
                 INNER JOIN
                     `group_action`
                 ON
                     `group_action`.`id_controller` = `controller`.`id`
                 INNER JOIN
                     `group`
                 ON
                     `group`.`id` = `group_action`.`id_group`
                 WHERE
                     `group`.`id` = ?i', $id_group);

        $rules = array();

        while ($data = $res->fetch_assoc())
        {
            if (!isset($rules[$data['module_key']]))
            {
                $rules[$data['module_key']] = array();
            }

            $rules[$data['module_key']][$data['controller_key']] = $data['access'];
        }

        return $rules;
    }

    /**
     * Устанавливает для пользователей группы с
     * идентификатором ID группу по умолчанию (user).
     *
     * @param int $id
     */
    function setDefaultUserGroupForGroupById($id)
    {
        return $this->db->query('UPDATE
                                     `user`
                                 SET
                                     `user_group` = (SELECT
                                                         `id`
                                                     FROM
                                                         `group`
                                                     WHERE
                                                         `group_alias` = "user")
                                 WHERE
                                     `user_group` = ?i
                                ', $id);
    }
}
?>