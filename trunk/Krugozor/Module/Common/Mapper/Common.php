<?php
/**
 * Подклассы класса Mapper_Abstract начиная с данного класса
 * являются интерфейсами над базовым классом.
 */
class Module_Common_Mapper_Common extends Mapper_Abstract
{
    /**
     * @see parent::createModelObject()
     * @param void
     * @return Base_Model
     */
    public function createNew()
    {
        return parent::createModel();
    }

    /**
     * Создает объект модели на основе данных $data, которые
     * являются объектом типа Cover_Array.
     *
     * @param object Cover_Array
     * @param array $mustbe список имён свойств, которые должны содержаться в создаваемом объекте
     *              и, по идее, должны присутствовать в $data. Т.е. это ожидаемый моделью список
     *              из POST-запроса. Если в $data эти свойства будут отсутствовать (попытка взлома),
     *              то свойства, перечисленные в $mustbe должны быть присвоены объекту согласно значениям
     *              по умолчанию, прописанным в Base_Model::$model_attributes
     *              Важное примечание: $mustbe не занимается ограничением свойств, т.е. если вы
     *              хотите из POST-запроса array('name' => '...', 'age' => '...') передать в объект
     *              ТОЛЬКО свойство name, то вам нет смысла писать $mustbe как array('name') -
     *              в объект будет по-любому передано свойство age, т.к. оно является частью
     *              модели и упоминается в Base_Model::$model_attributes.
     *              Для устранения определенных нежелательных свойств необходимо
     *              явно удалять их из массива $data.
     * @return object
     */
    public function createFromCover(Cover_Array $data, array $mustbe=array())
    {
        $object = new $this->model_class_name();
        $object->setMapperManager($this->manager);

        foreach ($data as $key => $value)
        {
            if ($key = $object->getMethodNameByKeyWithPrefix($key, 'set'))
            {
                $object->$key($value);
            }
        }

        if ($mustbe)
        {
	        // ключи ожидаемых значений, но не пришедших по каким-либо причинам из запроса
	        $mustbe = array_diff($mustbe, array_keys($data->getData()));

	        foreach ($mustbe as $key)
	        {
	            if ($method_name = $object->getMethodNameByKeyWithPrefix($key, 'set'))
	            {
	                $params = $object->getMapItem($key);

	                $value = isset($params['default_value'])
	                         ? $params['default_value']
	                         : null;

	                $object->$method_name($value);
	            }
	        }
        }

        return $object;
    }

    /**
     * @see parent::findModelByParams()
     * @param array
     * @return object
     */
    public function findByParams(array $params=array())
    {
        return parent::findModelByParams($params);
    }

    /**
     * Находит объект на основании параметра $objId.
     * $objId может быть либо объектом со свойством id,
     * либо числовым значением.
     * Перед тем, как выполнить SQL-запрос к базе,
     * метод сначала ищет объект с данным ID в
     * коллекции объектов self::$collection.
     *
     * @param object|int
     * @return object
     */
    public function findById($objId)
    {
        $id = is_object($objId) ? $objId->getId() : $objId;

        if (!$id)
        {
            return $this->createNew();
        }

        if (!isset(self::$collection[$this->getModuleName()][$this->getModelName()][$id]))
        {
            $object = parent::findModelByParams
            (
                array('where' => array('id = ?i' => array( $id )))
            );

            if ($object->getId())
            {
                return self::$collection[$this->getModuleName()][$this->getModelName()][$id] = $object;
            }
            else
            {
                return $object;
            }
        }

        return self::$collection[$this->getModuleName()][$this->getModelName()][$id];
    }

    /**
     * Метод находит объект на основании SQL запроса.
     *
     * @param $sql SQL запрос
     * @return object

    public function findBySql($sql='')
    {
        $res = $this->db->query($sql);

        if (is_object($res) && $res->getNumRows())
        {
            $object = parent::createModelFromArray($res->fetch_assoc());

            self::$collection[$this->getModuleName()][$this->getModelName()][$object->getId()] = $object;

            return $object;
        }

        return $this->createNew();
    }*/

    /**
     * @see parent::findModelList()
     * @param array параметры выборки
     * @return Cover_Array
     */
    public function findList(array $params=array())
    {
        return parent::findModelList($params);
    }

    /**
     * Получает значение FOUND_ROWS()
     *
     * @param void
     * @return int
     * @todo: для чего это нужно здесь?
     */
    public function getFoundRows()
    {
        return $this->db->query('SELECT FOUND_ROWS()')->getOne();
    }

    /**
     * Сохраняет объект в БД.
     *
     * @param object
     * @return void
     */
    public function save(Base_Model $object)
    {
        parent::saveModel($object);
    }

    /**
     * Удаляет запись(и) из таблицы согласно массиву параметров $params.
     *
     * @param array
     * @return int количество удаленных рядов
     */
    public function deleteByParams(array $params=array())
    {
        parent::createQueryDelete($params);

        return $this->db->getAffectedRows();
    }

    /**
     * Удаляет одну запись из таблицы согласно $objId.
     * $objId может быть либо объектом со свойством ID,
     * либо числовым значением.
     *
     * @param object|int
     * @return int количество удаленных рядов
     */
    public function deleteById($objId)
    {
        $params = array('where' => array('id = ?i' => array( is_object($objId) ? $objId->id : $objId) ),
                        'limit' => array('start' => 1));

        parent::createQueryDelete($params);

        return $this->db->getAffectedRows();
    }

    /***
     * Методы для работы с "весом" строки в списке строк.
     *
     * Описание: После добавлении статьи берется значение поля id (autoincrement)
     * добавленной статьи и дублируется в поле order_id.
     * При нажатии кнопки "вверх" на текущей статье -
     * 1. беру максимальное предыдущее значение order_id не равное текущему (обменная статья)
     * 2. меняю order_id обменной статьи на временное (0)
     * 3. меняю order_id текущей статьи на order_id обменной статьи
     * 4. меняю order_id обменной статьи на order_id текущей статьи
     */

    /**
     * Поднимает запись в иерархии на одну позицию выше.
     * Используя метод, нужно, быть уверенным в том,
     * что в таблице есть поле `order` предназначенное для сортировки.
     *
     * @param Base_Model
     * @return void
     */
    public function motionUp(Base_Model $object, array $category=array())
    {
        $sql_category = '';

        if ($category)
        {
            list($field, $value) = $category;
            $sql_category = ' AND `' . $field.'` = ' . $value;
        }

        $res = $this->db->query('SELECT
                                    `id`,
                                    `order`
                                FROM
                                    `' . $this->db_table_name . '`
                                WHERE
                                    `order` >
                                    (
                                        SELECT
                                            `order`
                                        FROM
                                            `' . $this->db_table_name . '`
                                        WHERE
                                            `id` = ?i
                                        ' . $sql_category . '
                                    )
                                ' . $sql_category . '
                                ORDER BY `order` ASC
                                LIMIT 0, 1', $object->getId());

        list($down_id, $new_order) = $res->fetch_row();

        if ($down_id && $new_order)
        {
            $res = $this->db->query('SELECT
                                        `order`
                                    FROM
                                        `' . $this->db_table_name . '`
                                    WHERE
                                        `id` = ?i ' . $sql_category, $object->getId());

            $down_order = $res->getOne();

            $this->db->query('UPDATE
                                 `' . $this->db_table_name . '`
                             SET
                                 `order` = ?i
                             WHERE
                                 `id` = ?i' . $sql_category, $down_order, $down_id);

            $this->db->query('UPDATE
                                 `' . $this->db_table_name . '`
                             SET
                                 `order` = ?i
                             WHERE
                                 `id` = ?i' . $sql_category, $new_order, $object->getId());
        }
    }

    /**
     * Опускает запись в иерархии на одну позицию ниже.
     * Используя метод, нужно, быть уверенным в том,
     * что в таблице есть поле `order` предназначенное для сортировки.
     *
     * @param Base_Model
     * @return void
     */
    public function motionDown(Base_Model $object, array $category=array())
    {
        $sql_category = '';

        if ($category)
        {
            list($field, $value) = $category;
            $sql_category = ' AND `' . $field.'` = ' . $value;
        }

        $res = $this->db->query('SELECT
                                    `id`,
                                    `order`
                                FROM
                                    `' . $this->db_table_name . '`
                                WHERE
                                    `order` <
                                    (
                                        SELECT
                                            `order`
                                        FROM
                                            `' . $this->db_table_name . '`
                                        WHERE
                                            `id` = ?i
                                       )
                                ' . $sql_category . '
                                ORDER BY
                                    `order` DESC
                                LIMIT 0, 1', $object->getId());

        list($up_id, $new_order) = $res->fetch_row();

        if ($up_id && $new_order)
        {
            $res = $this->db->query('SELECT
                                        `order`
                                    FROM
                                        `' . $this->db_table_name . '`
                                    WHERE
                                        `id` = ?i' . $sql_category, $object->getId());

            $up_order = $res->getOne();

            $this->db->query('UPDATE
                                 `' . $this->db_table_name . '`
                             SET
                                 `order` = ?i
                             WHERE
                                 `id` = ?i' . $sql_category, $up_order, $up_id);

            $this->db->query('UPDATE
                                 `' . $this->db_table_name . '`
                             SET
                                 `order` = ?i
                             WHERE
                                 `id` = ?i' . $sql_category, $new_order, $object->getId());
        }
    }

    /**
     * Обновляет поле `order` таблицы $this->db_table_name
     * на ID только что вставленной записи.
     * Вызывается сразу после метода save.
     *
     * @param Base_Model
     * @return Base_Model
     */
    protected function updateOrderField(Base_Model $object)
    {
        $fields_db = $this->db->getListFields($this->db_table_name);

        if (!empty($fields_db['order']))
        {
            $this->db->query('UPDATE
                                  `' . $this->db_table_name . '`
                              SET
                                  `order` = ?i
                              WHERE
                                  `id` = ?i', $object->getId(), $object->getId());
        }

        return $object;
    }

    protected function init(){}
}