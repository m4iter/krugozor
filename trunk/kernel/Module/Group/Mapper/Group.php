<?php
class Module_Group_Mapper_Group extends Module_Common_Mapper_Common
{
    public function __construct()
    {
        parent::__construct();
        $this->db_table_name = 'group';
        $this->model_class_name = 'Module_Group_Model_Group';
    }

    /**
     * Получает объект типа Cover_Var представляющий
     * собой список объектов типа Module_Group_Model_Group
     * за исклюением объекта группы гости.
     *
     * @access public
     * @param void
     * @return void
     */
    public function getAllGroupsWithoutGuest()
    {
        return parent::findModelList(array('where' => 'group_alias <> "guest"'));
    }

    /**
     * Ищет группу по параметру group_alias
     *
     * @access public
     * @param string $group_alias алиас группы
     * @return object Module_Group_Model_Group
     */
    public function findGroupByAlias($group_alias)
    {
        return parent::findModelByParams(array('where' => array('group_alias = "?s"' => array($group_alias))));
    }

    /**
     * Удаляет группу $group.
     *
     * @access public
     * @param object $group
     * @return void
     */
    public function delete(Base_Model $group)
    {
        parent::deleteById($group);

        $access = new Base_Access();
        $access->clearGroupRulesById($group->getId());
        $access->setDefaultUserGroupForGroupById($group->getId());
    }

    /**
     * Сохраняет группу $group
     *
     * @access public
     * @param object $group
     * @return void
     */
    public function save(Base_Model $group)
    {
        parent::save($group);

        if ($group->getRules())
        {
            $access = new Base_Access();
            $access->clearGroupRulesById($group->getId());
            $access->saverGroupRulesById($group->getId(), $group->getRules());
        }
    }
}
?>