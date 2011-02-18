<?php
class Module_User_Model_City extends Base_Model
{
    protected static $db_field_prefix = 'city';

    protected static $model_attributes = array
    (
        'id' => array('db_element' => FALSE,
                      'default_value' => 0),

        'id_region' => array('db_element' => TRUE,
                              'default_value' => 0,
                              'db_field_name' => 'city_id_region'),

        'id_country' => array('db_element' => TRUE,
                              'default_value' => 0,
                              'db_field_name' => 'city_id_country'),

        'name_ru' => array('db_element' => TRUE,
                           'db_field_name' => 'city_name_ru'),

        'name_en' => array('db_element' => TRUE,
                           'db_field_name' => 'city_name_en'),
    );
}