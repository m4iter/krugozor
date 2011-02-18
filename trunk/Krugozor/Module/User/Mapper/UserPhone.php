<?php
exit;
class Module_User_Mapper_UserPhone extends Module_Common_Mapper_Common
{
    public function __construct()
    {
        parent::__construct();
        $this->db_table_name = 'user_phone';
        $this->model_class_name = 'Module_User_Model_UserPhone';
    }

    public function deleteByParams(array $params=array())
    {
        $params = self::makeSqlFromParams($params);

        $sql = 'DELETE FROM `'.$this->db_table_name.'` '.$params['where'].$params['limit'];

        array_unshift($params['args'], $sql);

        call_user_func_array(array($this->db, 'query'), $params['args']);

        return $this->db->getAffectedRows();
    }
}
?>