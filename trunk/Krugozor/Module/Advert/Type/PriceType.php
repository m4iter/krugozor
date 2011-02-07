<?php
class Module_Advert_Type_PriceType implements Module_Common_Type_Interface
{
    protected $price_type;

    protected static $price_types = array
    (
        'rur' => array('рубли', 'руб.'),
        'usd' => array('доллары США', '$'),
        'eur' => array('евро', '&euro;')
    );

    public function __construct($price_type)
    {
        $this->price_type = $price_type;
    }

    public function getValue()
    {
        return $this->price_type;
    }

    public function getAsSymbol()
    {
        return isset(self::$price_types[$this->price_type])
               ? self::$price_types[$this->price_type][1]
               : null;
    }

    public static function getTypes()
    {
        return self::$price_types;
    }
}