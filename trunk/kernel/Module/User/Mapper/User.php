<?php
class Module_User_Mapper_User extends Module_Common_Mapper_Common
{
    public function __construct()
    {
        parent::__construct();

        $this->db_table_name = 'user';

        $this->model_class_name = 'Module_User_Model_User';
    }

    /**
     * @see parent::createFromCover
     * @param Cover_Array $data
     * @param array $except
     */
    public function createFromCover(Cover_Array $data, array $except=array())
    {
        $user = parent::createFromCover($data, $except);

        $data->age_day = (int)$data->age_day;
        $data->age_month = (int)$data->age_month;
        $data->age_year = (int)$data->age_year;

        if ($data->age_day && $data->age_month && $data->age_year)
        {
            $age = Module_Common_Type_Datetime::createFromFormat(
                       'j-n-Y H:i:s',
                       $data->age_day . '-'
                       . $data->age_month . '-'
                       . $data->age_year . ' 00:00:00'
                   );

            $user->setAge($age);
        }

        return $user;
    }

    /**
     * @see parent::deleteById
     * @param Module_User_Model_User $user
     */
    public function delete(Module_User_Model_User $user)
    {
        parent::deleteById($user);
    }

    /**
     * Возвращает массив данных пользователей для вывода
     * в списке админ-интерфейса.
     *
     * @param array
     * @return Cover_Array
     */
    public function getUsersListWithResidence($params = array())
    {
        /*if (!empty($params['where']))
        {
            $params['where']['AND'] = array();
        }*/

        $params['where']['`user`.`id` <> ?i'] = array(-1);

        $params = self::makeSqlFromParams($params);

        $sql = 'SELECT SQL_CALC_FOUND_ROWS
                    `user`.`id`,
                    `user`.`user_ip`,
                    `user`.user_active,
                    `user`.user_first_name,
                    `user`.user_last_name,
                    `user`.user_login,
                    `user`.user_last_name,
                    `user`.user_regdate,
                    `user`.user_visitdate,
                    `user`.user_mail,
                    `user`.user_url,
                    `user`.user_icq,
                    `user`.user_city,
                    `user`.user_region,
                    `user`.user_country,
                    `user_country`.id,
                    `user_country`.`country_name_ru`,
                    `user_region`.`id`,
                    `user_region`.`region_name_ru`,
                    `user_city`.`id`,
                    `user_city`.`city_name_ru`
                FROM
                    `user`
                LEFT JOIN
                    `user_country`
                ON
                    `user`.`user_country` = `user_country`.`id`
                LEFT JOIN
                    `user_region`
                ON
                    `user`.`user_region` = `user_region`.`id`
                LEFT JOIN
                    `user_city`
                ON
                    `user`.`user_city` = `user_city`.`id`'.
                $params['where'].
                $params['order'].
                $params['limit'];

        array_unshift($params['args'], $sql);

        return parent::result2objects(call_user_func_array(array($this->db, 'query'), $params['args'])->getResult());
    }

    /**
     * Возвращает доменный объект Module_User_Model_User находя его по $id,
     * хешу md5 и соли $salt.
     * Используется при авторизации через куки (автологин).
     *
     * @param int $id
     * @param string $md5password
     * @param string $salt
     * @return Module_User_Model_User
     */
    public function findByLoginHash($id, $md5password, $salt)
    {
         $res = $this->db->query('SELECT * FROM
                                      `'.$this->db_table_name.'`
                                  WHERE
                                      id = ?i
                                  AND
                                      MD5(CONCAT(`user_login`, `user_password`, "?s")) = "?s"
                                 ', $id,
                                    $salt,
                                    $md5password);

        if (is_object($res) && $res->getNumRows() > 0)
        {
            return parent::createModelFromArray($res->fetch_assoc());
        }

        return false;
    }

    /**
     * Возвращает доменный объект User находя его по логину.
     *
     * @param string
     * @return Module_User_Model_User
     */
    public function findByLogin($login)
    {
        $params = array
        (
            'where' => array('`user_login` = "?s"' => array($login))
        );

        return parent::findByParams($params);
    }

    /**
     * Возвращает доменный объект User находя его по mail.
     *
     * @param string
     * @return Module_User_Model_User
     */
    public function findByMail($mail)
    {
        $params = array
        (
            'where' => array('`user_mail` = "?s"' => array($mail))
        );

        return parent::findByParams($params);
    }

    /**
     * Возвращает доменный объект User находя его по $login и $password.
     * Используется при авторизации из POST.
     *
     * @param string $login логин из POST-запроса
     * @param string $password пароль из POST-запроса
     * @return Module_User_Model_User
     */
    public function findByLoginPassword($login, $password)
    {
        $params = array
        (
            'where' => array('`user_login` = "?s" AND MD5("?s") = `user_password`' => array($login, $password))
        );

        return parent::findByParams($params);
    }

    /**
     * Обновляет актуальную информацию о пользователе.
     *
     * @param Module_User_Model_User
     * @return void
     */
    public function updateActualInfo($user)
    {
        $this->db->query('UPDATE
                              `'.$this->db_table_name.'`
                          SET
                              `user_visitdate` = NOW(),
                              `user_ip` = "?s"
                          WHERE
                               `id` = ?i
                         ', $_SERVER['REMOTE_ADDR'], $user->getId());
    }
}
?>