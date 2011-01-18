<?php
/**
 * Абстрактный класс реализующий интерфейс Cover_Interface.
 * Данный класс является базовым для многих классов системы.
 */
abstract class Cover_Abstract_Simple implements Cover_Interface
{
    protected $data = array();

    public function __get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    public function __unset($key)
    {
        unset($this->data[$key]);
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        foreach ($data as $key => $value)
        {
            $this->$key = $value;
        }
    }

    public function clear()
    {
        $this->data = array();
    }

    public function __toString(){}
}