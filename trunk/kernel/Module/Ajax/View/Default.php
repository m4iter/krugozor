<?php
class Module_Ajax_View_Default extends Base_View
{
    /**
     * Преобразует строку $value, представляющую
     * собой строку в кодировке windows-1251 в UTF-8.
     *
     * @access protected
     * @param string
     * @return string
     */
    protected function win2utfSc($value)
    {
        return htmlspecialchars(iconv('windows-1251', 'utf-8//ignore', $value));
    }

    /**
     * Создает строку JSON из многомерного объекта Cover_Array.
     *
     * @access protected
     * @param Cover_Array
     * @return string
     */
    protected function createJsonList(Cover_Array $data)
    {
        $str = '{';

        foreach ($data as $cover)
        {
            $str .= '"'.$cover->key.'":"'.addslashes($cover->value).'",';
        }

        return rtrim($str, ',').'}';
    }

    /**
     * Создает строку JSON из объекта Cover_Array.
     *
     * @access protected
     * @param object Cover_Array
     * @return string
     */
    protected function createJsonSimple(Cover_Array $data)
    {
        return '{"'.$data['key'].'":"'.addslashes($data['value']).'"}';
    }
}
?>