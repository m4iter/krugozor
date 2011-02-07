<?php
class Module_User_Mapper_Region extends Module_Common_Mapper_Common
{
    public function init()
    {
        $this->db_table_name = 'user_region';
        $this->model_class_name = 'Module_User_Model_Region';
    }

    /**
     * Возвращает массив вида ид_региона => наименование_региона.
     *
     * @access public
     * @param void
     * @return array
     */
    public function getArrayListForHtmlSelect($id_country=1)
    {
        if (!Base_Numeric::is_decimal($id_country))
        {
            $id_country = 1;
        }

        return $this->db->query('
            SELECT
                `id` as `key`,
                `region_name_' . Base_Registry::getInstance()->config->lang . '` AS `value`
            FROM
                `' . $this->db_table_name . '`
            WHERE
                `id_country` = ?i
            ORDER BY
                `key` ASC,
                `value` ASC
            ', $id_country)->fetch_assoc_array();
    }
}