<?php
class Module_User_Mapper_Country extends Module_Common_Mapper_Common
{
    public function init()
    {
        $this->db_table_name = 'user_country';
        $this->model_class_name = 'Module_User_Model_Country';
    }

    /**
     * Возвращает многомерный массив вида ид_страны => наименование_страны.
     *
     * @access public
     * @param void
     * @return array
     */
    public function getArrayListForHtmlSelect()
    {
        return $this->db->query('
            SELECT
                `id` as `key`,
                `country_name_' . Base_Registry::getInstance()->config->lang . '` AS `value`
            FROM
                `' . $this->db_table_name . '`
            ORDER BY
                `id` ASC
            ')->fetch_assoc_array();
    }
}