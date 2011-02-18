<?php
class Module_Common_Validator_VarEnum extends Validator_Abstract
{
    /**
     * Массив, который проходит проверку на наличие в нем значения $this->value
     *
     * @var array
     */
    private $enum;

    /**
     * @param $value значение, которое должно присутствовать во множестве $enum
     * @param $enum
     * @param $_break
     * @param $ERROR_KEY
     */
    public function __construct($value, $enum=array(), $_break=TRUE, $ERROR_KEY='INCORRECT_VALUE')
    {
        parent::init($value, $_break, $ERROR_KEY);

        $this->setEnum($enum);
    }

    /**
     * @param array $enum
     * @return Module_Common_Validator_VarEnum
     */
    public function setEnum(array $enum)
    {
        $this->enum = (array)$enum;

        return $this;
    }

    /**
     * @see Krugozor/Validator/Validator_Abstract#validate()
     */
    public function validate()
    {
        if (Base_String::isEmpty($this->value)) {
            return true;
        }

        return in_array($this->value, $this->enum);
    }
}