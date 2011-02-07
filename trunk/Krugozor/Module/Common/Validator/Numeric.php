<?php
class Module_Common_Validator_Numeric extends Validator_Abstract
{
    /**
     * @param string|int $value
     * @param boolean $_break
     * @param string $ERROR_KEY
     */
    public function __construct($value, $_break=TRUE, $ERROR_KEY='INVALID_NUMERIC')
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

        return is_numeric($this->value);
    }
}