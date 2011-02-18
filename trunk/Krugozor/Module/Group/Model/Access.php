<?php
class Module_Group_Model_Access extends Base_Model
{
    protected static $db_field_prefix = 'group_access';

    protected static $model_attributes = array
    (
        'id_group' => array('db_element' => true,
                            'db_field_name' => 'id_group',
                            'validators' => array(
                                'Common/EmptyNull' => array(),
                                'Common/Decimal' => array('unsigned' => true),
                            )
                      ),

        'id_controller' => array('db_element' => true,
                                 'db_field_name' => 'id_controller',
                                 'validators' => array(
                                     'Common/EmptyNull' => array(),
                                     'Common/Decimal' => array('unsigned' => true),
                                )
                           ),

        'access' => array('db_element' => true,
                          'db_field_name' => 'access',
                          'validators' => array(
                              'Common/EmptyNull' => array(),
                              'Common/Decimal' => array('unsigned' => true),
                         )
                    ),
    );
}