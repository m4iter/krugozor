<?php
class Module_User_Mapper_City extends Module_Common_Mapper_Common
{
    public function __construct()
    {
        parent::__construct();
        $this->db_table_name = 'user_city';
        $this->model_class_name = 'Module_User_Model_City';
    }

    /**
     * Возвращает массив вида ид_города => наименование_города.
     *
     * @access public
     * @param void
     * @return array
     */
    public function getArrayListForHtmlSelect($id_region=1)
    {
        if (!Base_Numeric::is_decimal($id_region))
        {
            $id_region = 1;
        }

        return $this->db->query('SELECT
                                    `id` as `key`,
                                    `city_name_'.Base_Registry::getInstance()->config->lang.'` AS `value`
                                 FROM
                                    `'.$this->db_table_name.'`
                                 WHERE
                                    `id_region` = ?i
                                 ORDER BY
                                    `id`', $id_region)->fetch_assoc_array();
    }
}
?>