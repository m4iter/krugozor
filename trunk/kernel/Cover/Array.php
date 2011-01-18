<?php
class Cover_Array extends Cover_Abstract_Array
{
    public function __construct(array $data=array())
    {
        foreach ($data as $key => $value)
        {
            $this->data[$key] = is_array($value) ? new $this($value) : $value;
        }
    }
}
?>