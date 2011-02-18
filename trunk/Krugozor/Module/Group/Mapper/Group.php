<?php
class Module_Group_Mapper_Group extends Module_Common_Mapper_Common
{
    protected function init()
    {
        $this->db_table_name = 'group';
        $this->model_class_name = 'Module_Group_Model_Group';
    }
    
    /**
     * @see Krugozor/Module/Common/Mapper/Module_Common_Mapper_Common#createFromCover()
     */
    public function createFromCover(Cover_Array $data, array $mustbe=array())
    {
        $group = parent::createFromCover($data, $mustbe);

        if ($data['group_access'])
        {
	        foreach ($data['group_access'] as $id_module => $access_data)
	        {
	            foreach ($access_data as $id_controller => $accessValue)
	            {
		            $access = $this->manager->getMapper('Group/Access')->createNew();
		            $access->setIdGroup($group->getId())
	                       ->setIdController($id_controller)
	                       ->setAccess($accessValue);
		            $group->setAccess($access);
	            }
	        }
        }

        return $group;
    }

    /**
     * Сохраняет группу $group и её доступы, если они есть.
     *
     * @param Base_Model $group
     * @return void
     */
    public function save(Base_Model $group)
    {
        parent::save($group);
        $this->manager->getMapper('Group/Access')->saveAccesses($group);

    }

    /**
     * Удаляет группу $group, её доступы и связывает пользователей,
     * закрепленных за этой группой, с группой "Пользователи".
     *
     * @param Base_Model $group
     * @return void
     */
    public function delete(Base_Model $group)
    {
        parent::deleteById($group);
        $this->manager->getMapper('Group/Access')->clearByGroup($group);
        $this->manager->getMapper('User/User')->setDefaultGroupForUsersWithGroup($group);
    }
    
    /**
     * Находит все группы, за исклюением группы гостей.
     *
     * @param void
     * @return Cover_Array
     */
    public function findAllGroupsWithoutGuest()
    {
        return parent::findModelList(array('where' => 'group_alias <> "guest"'));
    }

    /**
     * Ищет группу по алиасу группы.
     *
     * @param string $group_alias алиас группы
     * @return Module_Group_Model_Group
     */
    public function findGroupByAlias($group_alias)
    {
        return parent::findModelByParams(array('where' => array('group_alias = "?s"' => array($group_alias))));
    }
}