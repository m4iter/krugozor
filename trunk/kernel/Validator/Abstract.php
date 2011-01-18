<?php
abstract class Validator_Abstract
{
    /**
     * ����������� �������� ����������.
     *
     * @access protected
     * @var mixed
     */
    protected $value;

    /**
     * ��� ������
     *
     * @access protected
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
     * @access protected
     * @var string|array
     */
    protected $error;

    /**
     * �������� �� �� ������� ������� ����������� � ���� ������,
     * ���� ���� ��������� ����� ������.
     * TRUE - ��������, FALSE - ���.
     *
     * @access protected
     * @var boolean
     */
    protected $_break;

    /**
     * ������ ����� ���������� � ��������� ������������ ����������.
     *
     * @param mixed $value ����������� ��������
     * @param bool $_break ����� ���������, ���������
     * �������� �� �������� ����������, ���� ������� ���������
     * ��������� ������
     * @param string $ERROR_KEY ���� �������� ������
     * @return void
     */
    public function init($value, $_break, $ERROR_KEY)
    {
        $this->value = $value;
        $this->ERROR_KEY = $ERROR_KEY;
        $this->_break = $_break;
    }

    /**
     * ���������� ��������� ��������.
     *
     * @access public
     * @param void
     * @return bool TRUE ���� ������ ���, FALSE � ���� ������
     * @abstract
     */
    abstract public function validate();

    /**
     * ���������� ������ �������� ����������.
     *
     * @access public
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
     * ���������� ��������� $_break.
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
     * ������������� ��������� $_break.
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