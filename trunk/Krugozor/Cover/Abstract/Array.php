<?php
/**
 * Обёртка массива, хранилище.
 * Попытка реализации объекта для более удобной работы с массиво-образной структурой данных
 * в рамках OO-ориентированных PHP-приложений.
 */
abstract class Cover_Abstract_Array extends Cover_Abstract_Simple
implements IteratorAggregate, Countable, ArrayAccess, Serializable
{
    public function __set($key, $value)
    {
        $this->data[$key] = is_array($value) ? new $this($value) : $value;
    }

    public function __toString()
    {
        return ''; // $this->data ? strval(current($this->data)) : '';
    }

    /**
     * Реализация интерфейса Countable
     *
     * @access public
     * @param void
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * Реализация интерфейса IteratorAggregate
     *
     * @access public
     * @param void
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    /**
     * Возвращает элемент коллекции с заданным индексом в качестве результата.
     * Аналог parent::__get, но предназначен для числовых индексов.
     *
     * @access public
     * @param mixed
     * @return mixed
     */
    public function item($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Присоединяет один элемент в начало массива.
     *
     * @access public
     * @param mixed
     * @return object
     */
    public function prepend($value)
    {
        array_unshift($this->data, self::array2cover($value));

        return $this;
    }

    /**
     * Присоединяет один элемент в конец массива.
     *
     * @access public
     * @param mixed
     * @return object
     */
    public function append($value)
    {
        array_push($this->data, self::array2cover($value));

        return $this;
    }

    /**
     * Возвращает последний элемент массива.
     *
     * @param void
     * @return mixed
     */
    public function getLast()
    {
        $last = end($this->data);
        reset($this->data);
        return $last;
    }

    /**
     * Возвращает первый элемент массива.
     *
     * @param void
     * @return mixed
     */
    public function getFirst()
    {
        $last = end($this->data);
        reset($this->data);
        return $last;
    }

    /**
     * Возвращает данные объекта как массив.
     *
     * @access public
     * @param void
     * @return array
     */
    public function getDataAsArray()
    {
        return self::object2array($this->data);
    }

    /**
     * Реализация метода интерфейса ArrayAccess.
     *
     * @param int|string|null $key ключ элемента
     * @param mixed $value значение элемента
     * @return Cover_Array
     */
    public function offsetSet($key, $value)
    {
        // Это присвоение нового элемента массиву типа $var[] = 'element';
        if ($key === null)
        {
            $u = &$this->data[];
        }
        else
        {
            $u = &$this->data[$key];
        }

        $u = self::array2cover($value);
    }

    /**
     * Реализация метода интерфейса ArrayAccess.
     *
     * @param int|string ключ элемента
     * @return boolean
     */
    public function offsetExists($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Реализация метода интерфейса ArrayAccess.
     *
     * @param int|string ключ элемента
     * @return void
     */
    public function offsetUnset($key)
    {
        if (isset($this->data[$key]))
        {
            unset($this->data[$key]);
        }
    }

    /**
     * Реализация метода интерфейса ArrayAccess.
     * Отличается от поведения ArrayObject тем, что в случае отсутствия
     * запрошеного элемента не генерирует ошибку, а создает в вызвавшем
     * его объекте, в хранилище, свойство $key содержащее пустой объект
     * текущего класса.
     *
     * @param int|string ключ элемента
     * @return mixed
     */
    public function offsetGet($key)
    {
        if (isset($this->data[$key]))
        {
            return $this->data[$key];
        }
        else
        {
            return $this->data[$key] = new Cover_Array();
        }
    }

    /**
     * Реализация метода интерфейса Serializable.
     *
     * @return array
     */
    public function serialize()
    {
        return serialize($this->data);
    }

    /**
     * Реализация метода интерфейса Serializable.
     *
     * @param array $data
     * @return Cover_Array
     */
    public function unserialize($data)
    {
        $this->setData(unserialize($data));

        return $this;
    }

    /**
     * Преобразует все значения массива $in в массивы, если значения
     * каких-либо элементов данных будут объекты типа Cover_Array.
     *
     * @access protected
     * @param array
     * @return array
     */
    protected static function object2array(array $in)
    {
        foreach ($in as $key => $value)
        {
            $in[$key] = (is_object($value) && $value instanceof self)
                        ? $in[$key] = self::object2array($value->getData())
                        : $value;
        }

        return $in;
    }

    /**
     * Возвращает объект Cover_Array, если переданным
     * в метод значением является массив.
     *
     * @access protected
     * @param mixed
     * @return mixed
     */
    protected static function array2cover($value)
    {
        return is_array($value) ? new Cover_Array($value) : $value;
    }
}