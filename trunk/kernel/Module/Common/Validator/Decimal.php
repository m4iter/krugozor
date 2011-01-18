<?php
class Module_Common_Validator_Decimal extends Validator_Abstract
{
    /**
     * Должно ли проверяемое значение быть беззнаковым числом.
     *
     * @var boolean
     */
    private $unsigned;

    public function __construct($value, $unsigned=TRUE, $_break=TRUE, $ERROR_KEY='INVALID_UNSIGNED_DECIMAL')
    {
        parent::init($value, $_break, $ERROR_KEY);

        $this->setUnsigned($unsigned);
    }

    public function setUnsigned($unsigned)
    {
        $this->unsigned = $unsigned;
    }

    public function validate()
    {
        if (Base_String::isEmpty($this->value)) {
            return true;
        }

        return Base_Numeric::is_decimal($this->value, $this->unsigned);
    }
}
?>