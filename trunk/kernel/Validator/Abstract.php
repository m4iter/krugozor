<?php
abstract class Validator_Abstract
{
    /**
     * Проверяемое значение переменной.
     *
     * @access protected
     * @var mixed
     */
    protected $value;

    /**
     * Код ошибки
     *
     * @access protected
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
     * @access protected
     * @var string|array
     */
    protected $error;

    /**
     * Выходить ли из разбора цепочки валидаторов с этим ключом,
     * если этот валидатор нашел ошибки.
     * TRUE - выходить, FALSE - нет.
     *
     * @access protected
     * @var boolean
     */
    protected $_break;

    /**
     * Данный метод вызывается в контексте конструктора наследника.
     *
     * @param mixed $value проверяемое значение
     * @param bool $_break булев указатель, говорящий
     * обрывать ли проверку переменной, если текущий валидатор
     * обнаружил ошибку
     * @param string $ERROR_KEY ключ описания ошибки
     * @return void
     */
    public function init($value, $_break, $ERROR_KEY)
    {
        $this->value = $value;
        $this->ERROR_KEY = $ERROR_KEY;
        $this->_break = $_break;
    }

    /**
     * Производит валидацию значения.
     *
     * @access public
     * @param void
     * @return bool TRUE если ошибок нет, FALSE в ином случае
     * @abstract
     */
    abstract public function validate();

    /**
     * Возвращает ошибку текущего валидатора.
     *
     * @access public
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
     * Возвращает состояние $_break.
     *
     * @access public
     * @param void
     * @return bool
     */
    public function getBreak()
    {
        return $this->_break;
    }

    /**
     * Устанавливает состояние $_break.
     *
     * @access public
     * @param bool
     * @return void
     */
    public function setBreak($in)
    {
        $this->_break = (bool)$in;
    }
}
?>