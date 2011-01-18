<?php
class Module_Advert_Mapper_Advert extends Module_Common_Mapper_Common
{
    public function __construct()
    {
        parent::__construct();
        $this->db_table_name = 'advert';
        $this->model_class_name = 'Module_Advert_Model_Advert';
    }

    /**
     * Обновляет счётчик просмотров объявления на 1.
     *
     * @param Module_Advert_Model_Advert объект объявления
     * @return int
     */
    public function incrementViewCount(Module_Advert_Model_Advert $advert)
    {
        $sql = 'UPDATE `'.$this->db_table_name.'` SET `advert_view_count` = `advert_view_count`+1 WHERE id = ?i LIMIT 1';

        $this->db->query($sql, $advert->getId());

        return $this->db->getAffectedRows();
    }

    /**
     * Возвращает список объектов для вывода в административной части.
     *
     * @param array
     * @return Cover_Array
     */
    public function findListForBackend($params)
    {
        $params = self::makeSqlFromParams($params);

        $sql = 'SELECT SQL_CALC_FOUND_ROWS
                    `advert`.`id`,
                    `advert`.`advert_id_user`,
                    `advert`.`advert_active`,
                    `advert`.`advert_type`,
                    `advert`.`advert_category`,
                    `advert`.`advert_header`,
                    `advert`.`advert_user_name`,
                    `advert`.`advert_main_user_name`,
                    `advert`.`advert_create_date`,
                    `advert`.`advert_view_count`,
                    `category`.`id`,
                    `category`.`category_name`,
                    `category`.`category_url`,
                    `user`.`id`,
                    `user`.`user_first_name`,
                    `user`.`user_last_name`,
                    `user`.`user_login`
                FROM
                    `advert`
                LEFT JOIN
                    `category`
                ON
                    `advert`.`advert_category` = `category`.`id`
                LEFT JOIN
                    `user`
                ON
                    `advert`.`advert_id_user` = `user`.`id`
                '.
                $params['where'].
                $params['order'].
                $params['limit'];

        array_unshift($params['args'], $sql);

        return parent::result2objects(call_user_func_array(array($this->db, 'query'), $params['args'])->getResult());
    }

    public function findListForIndex($num=10)
    {
        $sql = 'SELECT
                    `advert`.`id`,
                    `advert`.`advert_type`,
                    `advert`.`advert_price`,
                    `advert`.`advert_price_type`,
                    `advert`.`advert_category`,
                    `advert`.`advert_header`,
                    `advert`.`advert_create_date`,
                    `advert`.`advert_text`,
                    `category`.`id`,
                    `category`.`category_name`,
                    `category`.`category_url`,
                    `user_country`.`id`,
                    `user_country`.`country_name_ru`,
                    `user_region`.`id`,
                    `user_region`.`id_country`,
                    `user_region`.`region_name_ru`,
                    `user_city`.`id`,
                    `user_city`.`id_region`,
                    `user_city`.`id_country`,
                    `user_city`.`city_name_ru`
                FROM
                    `advert`
                LEFT JOIN
                    `category`
                ON
                    `advert`.`advert_category` = `category`.`id`
                LEFT JOIN
                    `user_country`
                ON
                    `advert`.`advert_place_country` = `user_country`.`id`
                LEFT JOIN
                    `user_region`
                ON
                    `advert`.`advert_place_region` = `user_region`.`id`
                LEFT JOIN
                    `user_city`
                ON
                    `advert`.`advert_place_city` = `user_city`.`id`
                WHERE
                   `advert`.`advert_active` = 1
                ORDER BY
                    `advert`.`id` DESC
                LIMIT 0, ?i';

        return parent::result2objects(call_user_func_array(array($this->db, 'query'), array($sql, $num))->getResult());
    }

    /**
     * Возвращает список объектов объявлений, "похожих" на объявление
     * $advert пользователя $user.
     *
     * @param Module_Advert_Model_Advert $advert
     * @param Module_User_Model_User $user
     * @param int $limit количество возвращаемых записей
     */
    public function finfSimilarAdverts(Module_Advert_Model_Advert $advert, Module_User_Model_User $user, $limit=5, $start_date_interval=14, $stop_date_interval=14)
    {
        $params['limit'] = array('start' => 0, 'stop' => $limit);

        $params['where']['`advert_active` = 1 AND `advert_type` = "?s"'] = array($advert->getType());
        $params['where']['AND `advert`.`advert_category` = ?i'] = array($advert->getCategory());

        $place = array('city' => $advert->getPlaceCity() ?: $user->getCity(),
                       'region' => $advert->getPlaceRegion() ?: $user->getRegion(),
                       'country' => $advert->getPlaceCountry() ?: $user->getCountry());

        foreach ($place as $key => $value)
        {
            if ($value)
            {
                $params['where']['AND `advert`.`advert_place_'.$key.'` = ?i'] = array($value);
            }
        }

        $params['where']['AND
                              `advert`.`advert_create_date`
                          BETWEEN
                              ("?s" - INTERVAL ?i DAY)
                          AND
                              ("?s" + INTERVAL ?i DAY)'] = array($advert->getCreateDate()->format('Y-m-d H:i:s'),
                                                                 $start_date_interval,
                                                                 $advert->getCreateDate()->format('Y-m-d H:i:s'),
                                                                 $stop_date_interval);

        $params['where']['AND `advert`.`id` <> ?i'] = array($advert->getId());

        $params['what'] = '`advert`.`id`,
                           `advert`.`advert_header`,
                           `advert`.`advert_price`,
                           SUBSTRING(`advert`.`advert_text`, 1, 150) AS `advert_text`,
                           `advert`.`advert_create_date`';

        $params['order'] = array('advert.advert_create_date' => 'DESC');

        return parent::findList($params);
    }

    /**
     * Возвращает список объектов для вывода в каталоге.
     *
     * @param $params
     * @return Cover_Array
     */
    public function findListForCatalog($params)
    {
        $params['where']['AND `advert_active` = 1'] = array();

        $params = self::makeSqlFromParams($params);

        $sql = 'SELECT SQL_CALC_FOUND_ROWS
                    `advert`.`id`,
                    `advert`.`advert_type`,
                    `advert`.`advert_category`,
                    `advert`.`advert_header`,
                    `advert`.`advert_price`,
                    `advert`.`advert_price_type`,
                    `advert`.`advert_create_date`,
                    `advert`.`advert_text`,
                    `category`.`id`,
                    `category`.`category_name`,
                    `category`.`category_url`,
                    `user_country`.`id`,
                    `user_country`.`country_name_ru`,
                    `user_region`.`id`,
                    `user_region`.`id_country`,
                    `user_region`.`region_name_ru`,
                    `user_city`.`id`,
                    `user_city`.`id_region`,
                    `user_city`.`id_country`,
                    `user_city`.`city_name_ru`
                FROM
                    `advert`
                LEFT JOIN
                    `category`
                ON
                    `advert`.`advert_category` = `category`.`id`
                LEFT JOIN
                    `user_country`
                ON
                    `advert`.`advert_place_country` = `user_country`.`id`
                LEFT JOIN
                    `user_region`
                ON
                    `advert`.`advert_place_region` = `user_region`.`id`
                LEFT JOIN
                    `user_city`
                ON
                    `advert`.`advert_place_city` = `user_city`.`id`'.
                $params['where'].
                'ORDER BY `advert`.`advert_create_date` DESC'.
                $params['limit'];

        array_unshift($params['args'], $sql);

        return parent::result2objects(call_user_func_array(array($this->db, 'query'), $params['args'])->getResult());
    }

    /**
     * Возвращает список объектов для вывода в списке объявлений пользователя.
     *
     * @param $params
     * @return Cover_Array
     */
    public function findListForUser($id_user, $start, $stop)
    {
        $sql = 'SELECT SQL_CALC_FOUND_ROWS
                    `advert`.*,
                    `category`.`id`,
                    `category`.`category_name`,
                    `category`.`category_url`,
                    `user_country`.`id`,
                    `user_country`.`country_name_ru`,
                    `user_region`.`id`,
                    `user_region`.`id_country`,
                    `user_region`.`region_name_ru`,
                    `user_city`.`id`,
                    `user_city`.`id_region`,
                    `user_city`.`id_country`,
                    `user_city`.`city_name_ru`
                FROM
                    `advert`
                LEFT JOIN
                    `category`
                ON
                    `advert`.`advert_category` = `category`.`id`
                LEFT JOIN
                    `user_country`
                ON
                    `advert`.`advert_place_country` = `user_country`.`id`
                LEFT JOIN
                    `user_region`
                ON
                    `advert`.`advert_place_region` = `user_region`.`id`
                LEFT JOIN
                    `user_city`
                ON
                    `advert`.`advert_place_city` = `user_city`.`id`
                WHERE
                    `advert`.`advert_id_user` = ?i
                ORDER BY
                    `advert`.`advert_create_date` DESC
                LIMIT ?i, ?i';

        return parent::result2objects(call_user_func_array(array($this->db, 'query'), array($sql, $id_user, $start, $stop))->getResult());
    }

    /**
     * Находит объявление по ID и URL категории
     *
     * @param int ID объявления
     * @param string URL категории
     * @return Cover_Array
     */
    public function findByIdUrl($id, $category_url)
    {
        $sql = 'SELECT
                    `advert`.*,
                    `category`.`id`,
                    `category`.`category_name`,
                    `category`.`category_url`,
                    `category`.`category_keywords`,
                    `user`.id,
                    `user`.`user_first_name`,
                    `user`.`user_last_name`,
                    `user`.`user_mail`,
                    `user`.`user_phone`,
                    `user`.`user_icq`,
                    `user`.`user_url`,
                    `user`.`user_city`,
                    `user`.`user_region`,
                    `user`.`user_country`,
                    `user_country`.`id`,
                    `user_country`.`country_name_ru`,
                    `user_region`.`id`,
                    `user_region`.`id_country`,
                    `user_region`.`region_name_ru`,
                    `user_city`.`id`,
                    `user_city`.`id_region`,
                    `user_city`.`id_country`,
                    `user_city`.`city_name_ru`
                FROM
                    `advert`
                INNER JOIN
                    `category`
                ON
                    `advert`.`advert_category` = `category`.`id`
                LEFT JOIN
                    `user`
                ON
                    `advert`.`advert_id_user` = `user`.`id`
                LEFT JOIN
                    `user_country`
                ON
                    `advert`.`advert_place_country` = `user_country`.`id`
                LEFT JOIN
                    `user_region`
                ON
                    `advert`.`advert_place_region` = `user_region`.`id`
                LEFT JOIN
                    `user_city`
                ON
                    `advert`.`advert_place_city` = `user_city`.`id`
                WHERE
                    `advert`.`id` = ?i
                AND
                    `category`.`category_url` = "?s"
                LIMIT 1';

        $objects = parent::result2objects($this->db->query($sql, $id, $category_url)->getResult());

        if (!isset($objects[0]))
        {
            $objects[0] = array();
        }
        else
        {
            // Нужно убить из кэша категорию, что бы функции построения дерева могли взять актуальные
            // данные с загрузкой потомков.
            self::unsetCollectionElement('Category', 'Category', $objects[0]['category']->getId());
        }

        if (!isset($objects[0]['advert']))
        {
            $objects[0]['advert'] = new Module_Advert_Model_Advert();
        }

        if (!isset($objects[0]['user']))
        {
            $objects[0]['user'] = new Module_User_Model_User();
        }

        if (!isset($objects[0]['user_country']))
        {
            $objects[0]['user_country'] = new Module_User_Model_Country();
        }

        if (!isset($objects[0]['user_region']))
        {
            $objects[0]['user_region'] = new Module_User_Model_Region();
        }

        if (!isset($objects[0]['user_city']))
        {
            $objects[0]['user_city'] = new Module_User_Model_City();
        }

        return $objects[0];
    }

    /**
     * Находит список объектов по ID категории.
     *
     * @param int ID категории
     * @return Cover_Array
     */
    public function findObjectsListByIdCategory($id)
    {
        $params = array
        (
            'where' => array(Module_Advert_Model_Advert::getMapItem('category')->getFieldName().' = ?i' => array($id))
        );

        return parent::findModelList($params);
    }

    /**
     * @see parent::createNew
     * @param Module_User_Model_User $user
     */
    public function createNew(Module_User_Model_User $user=null)
    {
        $advert = parent::createNew();

        if ($user === NULL)
        {
            $Module_User_Mapper_User = new Module_User_Mapper_User();
            $user = $Module_User_Mapper_User->findById(-1);
        }

        $advert->setIdUser($user->getId());
        $advert->setPlaceCountry($user->getCountry());
        $advert->setPlaceRegion($user->getRegion());
        $advert->setPlaceCity($user->getCity());

        return $advert;
    }

    /**
     * Обновляет дату создания объявления $advert на текущее время + $hour час - 1 секунда.
     *
     * @access public
     * @param Module_Advert_Model_Advert $advert
     * @param int $hour час времени
     * @param int количество задействованных (обновленных) в запросе строк
     */
    public function updateDateCreate(Module_Advert_Model_Advert $advert, $hour=1)
    {
        $this->db->query('UPDATE
                              `'.$this->db_table_name.'`
                          SET
                              `'.Module_Advert_Model_Advert::getMapItem('create_date')->getFieldName().'` = DATE_SUB(NOW(), INTERVAL 1 SECOND)
                          WHERE
                              `id` = ?i
                          AND
                              NOW() > DATE_ADD(`'.Module_Advert_Model_Advert::getMapItem('create_date')->getFieldName().'`, INTERVAL ?i HOUR)
                          ', $advert->id, $hour);

        return (int)$this->db->getAffectedRows();
    }
}
?>