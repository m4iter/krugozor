<?php
class Module_Common_Validator_CharPassword extends Validator_Abstract
{
    /**
     * @param string $value
     * @param boolean $_break
     * @param string $ERROR_KEY
     */
    public function __construct($value, $_break=TRUE, $ERROR_KEY='INVALID_STRING_CHAR_PASS')
    {
        parent::init($value, $_break, $ERROR_KEY);
    }

    /**
     * @see Krugozor/Validator/Validator_Abstract#validate()
     */
    public function validate()
    {
        if (Base_String::isEmpty($this->value)) {
            return true;
        }

        return self::isCorrectCharsPass($this->value);
    }

    /**
    * ���� � ������ ������� �������� �� 'a-z', '0-9', '_', '-'.
    * ���������� true � ������ ���� ���� ���������,
    * false � ��������� ������.
    *
    * @param string ����������� ������
    * @return boolean
    */
    public static function isCorrectCharsPass($in)
    {
        return !preg_match("~[^a-z0-9_-]+~i", $in);
    }
}