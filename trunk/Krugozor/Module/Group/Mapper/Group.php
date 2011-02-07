<?php
class Module_Group_Mapper_Group extends Module_Common_Mapper_Common
{
    protected function init()
    {
        $this->db_table_name = 'group';
        $this->model_class_name = 'Module_Group_Model_Group';
    }

    /**
     * �������� ������ ���� Cover_Var ��������������
     * ����� ������ �������� ���� Module_Group_Model_Group
     * �� ���������� ������� ������ �����.
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
     * ���� ������ �� ��������� group_alias
     *
     * @access public
     * @param string $group_alias ����� ������
     * @return object Module_Group_Model_Group
     */
    public function findGroupByAlias($group_alias)
    {
        return parent::findModelByParams(array('where' => array('group_alias = "?s"' => array($group_alias))));
    }

    /**
     * ���������� ����� ������ � ���� ������������ �������.
     *
     * @see Base_Access::getGroupRulesById
     * @param int ID ������
     * @return array
     */
    public function findGroupRulesByGroupId($id)
    {
         $Base_Access = new Base_Access($this->db);
         return $Base_Access->getGroupRulesById($id);
    }

    /**
     * ������� ������ $group.
     *
     * @access public
     * @param object $group
     * @return void
     */
    public function delete(Base_Model $group)
    {
        parent::deleteById($group);

        $access = new Base_Access($this->db);
        $access->clearGroupRulesById($group->getId());
        $access->setDefaultUserGroupForGroupById($group->getId());
    }

    /**
     * ��������� ������ $group
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
            $access = new Base_Access($this->db);
            $access->clearGroupRulesById($group->getId());
            $access->saverGroupRulesById($group->getId(), $group->getRules());
        }
    }
}