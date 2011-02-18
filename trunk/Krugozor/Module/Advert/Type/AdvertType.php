<?php
class Module_Advert_Type_AdvertType implements Module_Common_Type_Interface
{
    protected $advert_type;

    protected static $advert_types = array
    (
        'sale' => 'Предложение',
        'buy' => 'Спрос',
    );

    public function __construct($advert_type)
    {
        $this->advert_type = $advert_type;
    }

    public function getValue()
    {
        return $this->advert_type;
    }

    public function getAsText()
    {
        return isset(self::$advert_types[$this->advert_type])
               ? self::$advert_types[$this->advert_type]
               : null;
    }

    public static function getTypes()
    {
        return self::$advert_types;
    }
}