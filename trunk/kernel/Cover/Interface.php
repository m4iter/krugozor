<?php
interface Cover_Interface
{
    public function __get($key);

    public function __set($key, $value);

    public function __isset($key);

    public function __unset($key);

    public function getData();

    public function setData($data);

    public function clear();

    public function __toString();
}
?>