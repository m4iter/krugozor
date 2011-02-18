<?php
/**
 * Абстрактный класс конкретного валидатора.
 *
 * Валидатор возвращает false в случае, если имеется факт ошибки
 * и true в обратном случае.
 */
abstract class Validator_Abstract
{
    /**
     * Проверяемое значение переменной.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Строка ошибки.
     *
     * @var string
     */
    protected $ERROR_KEY;

    /**
     * Ошибка текущего валидатора в совокупности со значением,
     * которое может быть выведено в тексте сообщения об ошибке.
     * Может иметь одно из значений:
     * 'ERROR_KEY' - ключ ошибки, аналог $ERROR_KEY
     * array('ERROR_KEY', array('key' => $value)) - массив,
     * содержащий как ключ ошибки, так и пару `ключ` => `значение`
     * для вывода в тексте сообщения об ошибке.
     *
     * @var string|array
     * @todo: может ли оно иметь скаляр, если да, исключить такую возможность
     */
    protected $error;

    /**
     * Выходить ли из цикла разбора цепочки валидаторов с этим ключом,
     * если этот валидатор нашел ошибки.
     * TRUE - выходить, FALSE - не выходить.
     *
     * @var boolean
     */
    protected $_break;

    /**
     * Mapper для работы с БД.
     * Передается через конструктор в тех валидаторах,
     * где он действительно необходим.
     *
     * @var Mapper_Abstract
     */
    protected $mapper;

    /**
     * Данный метод вызывается в контексте конструктора наследника.
     *
     * @param mixed $value проверяемое значение
     * @param bool $_break булев указатель, говорящий обрывать ли проверку
     * переменной, если текущий валидатор обнаружил ошибку.
     * @param string $error_key строка описания ошибки
     * @return Validator_Abstract
     * @todo: что такое ERROR_KEY и error?
     */
    public function init($value, $_break, $error_key)
    {
        $this->value = $value;
        $this->ERROR_KEY = $error_key;
        $this->_break = $_break;

        return $this;
    }

    /**
     * Производит валидацию значения.
     * Возвращает FALSE в случае обнаружения ошибки, TRUE в обратном случае.
     *
     * @param void
     * @return bool TRUE если ошибок нет, FALSE в ином случае
     * @abstract
     */
    abstract public function validate();

    /**
     * Возвращает ошибку текущего валидатора.
     *
     * @param void
     * @return array
     */
    public function getError()
    {
        // Если вызывается данный метод, то гарантированно validate() вернул
        // FALSE. Это значит, что ошибочная ситуация уже есть и нужно возвращать
        // информацию об ошибе. Но поскольку в примитивных валидаторах не
        // пишется явно присвоение переменной $this->error каких-либо значений,
        // то соответственно $this->error в таких случаях равен NULL.
        // Именно для таких ситуаций и предусмотрен код ниже, который в случае
        // явного отсутствия в $this->error какой-либо информации возвращает
        // станадартный массив вида array($this->ERROR_KEY, array())
        // Если же $this->error содержит лишь текстовую строку - ключ ошибки
        // $ERROR_KEY, то возвращаемая форма также приводится к стандартному массиву.
        return is_array($this->error)
               ? $this->error
               : ($this->error
                  ? array($this->error, array())
                  : array($this->ERROR_KEY, array())
                 );
    }

    /**
     * Возвращает состояние $this->_break.
     *
     * @param void
     * @return bool
     */
    public function getBreak()
    {
        return $this->_break;
    }

    /**
     * Устанавливает состояние $this->_break.
     *
     * @param bool
     * @return Validator_Abstract
     */
    public function setBreak($in)
    {
        $this->_break = (bool)$in;

        return $this;
    }
}