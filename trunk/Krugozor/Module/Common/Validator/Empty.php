<?php
class Module_Common_Validator_Empty extends Validator_Abstract
{
    /**
     * @param string|int $value
     * @param boolean $_break
     * @param string $ERROR_KEY
     */
    public function __construct($value, $_break=TRUE, $ERROR_KEY='EMPTY_VALUE')
    {
        parent::init($value, $_break, $ERROR_KEY);
    }

    /**
     * @see Krugozor/Validator/Validator_Abstract#validate()
     */
    public function validate()
    {
        return !empty($this->value);
    }
}