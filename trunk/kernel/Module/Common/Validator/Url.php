<?php
class Module_Common_Validator_Url extends Validator_Abstract
{
    public function __construct($value, $_break=TRUE, $ERROR_KEY='INVALID_STRING_URL')
    {
        parent::init($value, $_break, $ERROR_KEY);
    }

    public function validate()
    {
        if (Base_String::isEmpty($this->value)) {
            return true;
        }
        else
        {
            if ($this->value == 'http://')
            {
                return true;
            }
            else
            {
                return self::is_correct_url($this->value);
            }
        }
    }

    /**
    * ѕровер€ет строку на соответствие URL адресу
    * возвращает true в случае если ввод корректен
    * false в противном случае.
    *
    * @param string провер€ема€ строка
    * @return bool
    */
    public static function is_correct_url($in)
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
?>