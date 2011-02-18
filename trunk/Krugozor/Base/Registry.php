<?php
class Base_Registry extends Cover_Abstract_Array
{
    protected static $instance;

    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct(){}
}
?>