<?php
class Module_User_Type_Sex implements Module_Common_Type_Interface
{
    protected $sex;

    protected static $sex_types = array
    (
        'M' => '�������',
        'F' => '�������',
    );

    public function __construct($sex)
    {
        if (isset(self::$sex_types[$sex]))
        {
            $this->sex = $sex;
        }
    }

    public function getValue()
    {
        return $this->sex;
    }

    public function getAsText()
    {
        return self::$sex_types[$this->sex];
    }

    public static function getTypes()
    {
        return self::$sex_types;
    }
}