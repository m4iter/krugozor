<?php
class Module_Common_Validator_CharPassword extends Validator_Abstract
{
    public function __construct($value, $_break=TRUE, $ERROR_KEY='INVALID_STRING_CHAR_PASS')
    {
        parent::init($value, $_break, $ERROR_KEY);
    }

    public function validate()
    {
        if (Base_String::isEmpty($this->value)) {
            return true;
        }

        return self::is_correct_chars_pass($this->value);
    }

    /**
    * ���� � ������ ������� �������� �� 'a-z', '0-9', '_', '-' �
    * ���������� true � ������ ���� ���� ���������,
    * false � ��������� ������.
    *
    * @param string ����������� ������
    * @return bool
    */
    public static function is_correct_chars_pass($in)
    {
        return !preg_match("~[^a-z0-9_-]+~i", $in);
    }
}
?>