<?php
class Module_Common_Validator_Url extends Validator_Abstract
{
    /**
     * @param string $value
     * @param boolean $_break
     * @param string $ERROR_KEY
     */
    public function __construct($value, $_break=TRUE, $ERROR_KEY='INVALID_STRING_URL')
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
        else
        {
            return $this->value == 'http://' ? true : self::isCorrectUrl($this->value);
        }
    }

    /**
    * ѕровер€ет строку на соответствие URL адресу
    * возвращает true в случае если ввод корректен
    * false в противном случае.
    *
    * @param string $in провер€ема€ строка
    * @return boolean
    */
    public static function isCorrectUrl($in)
    {
        // return filter_var($in, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);

        $url_pattern = "#^
        (?:
            http://(?:www\.)?
            ([a-z0-9.\-]+)
            (:[0-9]+)?
            (/\S+)?
            [^\s.,'\"]
        )
        $#xi";

        return preg_match($url_pattern, $in);
    }
}