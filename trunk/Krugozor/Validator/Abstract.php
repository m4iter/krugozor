<?php
/**
 * ����������� ����� ����������� ����������.
 *
 * ��������� ���������� false � ������, ���� ������� ���� ������
 * � true � �������� ������.
 */
abstract class Validator_Abstract
{
    /**
     * ����������� �������� ����������.
     *
     * @var mixed
     */
    protected $value;

    /**
     * ������ ������.
     *
     * @var string
     */
    protected $ERROR_KEY;

    /**
     * ������ �������� ���������� � ������������ �� ���������,
     * ������� ����� ���� �������� � ������ ��������� �� ������.
     * ����� ����� ���� �� ��������:
     * 'ERROR_KEY' - ���� ������, ������ $ERROR_KEY
     * array('ERROR_KEY', array('key' => $value)) - ������,
     * ���������� ��� ���� ������, ��� � ���� `����` => `��������`
     * ��� ������ � ������ ��������� �� ������.
     *
     * @var string|array
     * @todo: ����� �� ��� ����� ������, ���� ��, ��������� ����� �����������
     */
    protected $error;

    /**
     * �������� �� �� ����� ������� ������� ����������� � ���� ������,
     * ���� ���� ��������� ����� ������.
     * TRUE - ��������, FALSE - �� ��������.
     *
     * @var boolean
     */
    protected $_break;

    /**
     * Mapper ��� ������ � ��.
     * ���������� ����� ����������� � ��� �����������,
     * ��� �� ������������� ���������.
     *
     * @var Mapper_Abstract
     */
    protected $mapper;

    /**
     * ������ ����� ���������� � ��������� ������������ ����������.
     *
     * @param mixed $value ����������� ��������
     * @param bool $_break ����� ���������, ��������� �������� �� ��������
     * ����������, ���� ������� ��������� ��������� ������.
     * @param string $error_key ������ �������� ������
     * @return Validator_Abstract
     * @todo: ��� ����� ERROR_KEY � error?
     */
    public function init($value, $_break, $error_key)
    {
        $this->value = $value;
        $this->ERROR_KEY = $error_key;
        $this->_break = $_break;

        return $this;
    }

    /**
     * ���������� ��������� ��������.
     * ���������� FALSE � ������ ����������� ������, TRUE � �������� ������.
     *
     * @param void
     * @return bool TRUE ���� ������ ���, FALSE � ���� ������
     * @abstract
     */
    abstract public function validate();

    /**
     * ���������� ������ �������� ����������.
     *
     * @param void
     * @return array
     */
    public function getError()
    {
        // ���� ���������� ������ �����, �� �������������� validate() ������
        // FALSE. ��� ������, ��� ��������� �������� ��� ���� � ����� ����������
        // ���������� �� �����. �� ��������� � ����������� ����������� ��
        // ������� ���� ���������� ���������� $this->error �����-���� ��������,
        // �� �������������� $this->error � ����� ������� ����� NULL.
        // ������ ��� ����� �������� � ������������ ��� ����, ������� � ������
        // ������ ���������� � $this->error �����-���� ���������� ����������
        // ������������ ������ ���� array($this->ERROR_KEY, array())
        // ���� �� $this->error �������� ���� ��������� ������ - ���� ������
        // $ERROR_KEY, �� ������������ ����� ����� ���������� � ������������ �������.
        return is_array($this->error)
               ? $this->error
               : ($this->error
                  ? array($this->error, array())
                  : array($this->ERROR_KEY, array())
                 );
    }

    /**
     * ���������� ��������� $this->_break.
     *
     * @param void
     * @return bool
     */
    public function getBreak()
    {
        return $this->_break;
    }

    /**
     * ������������� ��������� $this->_break.
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