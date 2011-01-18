<?php
class Module_Group_Model_Group extends Base_Model
{
    protected static $db_field_prefix = 'group';

    protected static $model_attributes = array
    (
        'id' => array('db_element'=>FALSE,
                      'default_value' => 0,
                      'validators' => array(
                          'Common/Decimal' => array('unsigned' => true),
                      )
                     ),

        'name' => array('db_element'=>TRUE,
                        'db_field_name'=>'group_name',
                        'validators' => array(
                          'Common/EmptyNull' => array(),
                        )
                       ),

        'active' => array('db_element'=>TRUE,
                                'db_field_name'=>'group_active',
		                        'validators' => array(
		                            'Common/EmptyNull' => array(),
                                    'Common/Decimal' => array('unsigned' => true),
                                    'Common/IntRange' => array('min' => 0, 'max' => 1),
		                        )
		                       ),

        'alias' => array('db_element'=>TRUE,
                               'db_field_name'=>'group_alias',
		                       'validators' => array(
		                           'Common/EmptyNull' => array(),
		                           'Common/CharPassword' => array(),
		                       )
		                      ),
    );

    /**
     * Многомерный масив прав.
     *
     * @var array
     */
    private $access;

    public function setRules($rules)
    {
        $this->access = $rules;
    }

    public function getRules()
    {
        if ($this->access === null)
        {
            $Base_Access = new Base_Access();
            $this->access = $Base_Access->getGroupRulesById($this->getId());
        }

        return $this->access;
    }

    /**
     * Возвращает логическое значение - "доступ" группы
     * к контроллеру $id_controller модуля $id_moduule
     *
     * @param int $id_module
     * @param int $id_contoller
     */
    public function getRuleByModuleControllerIds($id_module, $id_contoller)
    {
        if ($this->access === null)
        {
            $Base_Access = new Base_Access();
            $this->access = $Base_Access->getGroupRulesById($this->getId());
        }

        return isset($this->access[$id_module][$id_contoller])
               ? $this->access[$id_module][$id_contoller]
               : null;
    }
}
?>