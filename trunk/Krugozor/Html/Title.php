<?php
class Html_Title
{
    private $data = array();
    private $separator;
    private static $instance;

    public static function getInstance($separator = ' / ')
    {
        if (!self::$instance)
        {
            $class = __CLASS__;

            self::$instance = new $class($separator);
        }

        return self::$instance;
    }

    private function __construct($separator)
    {
        $this->separator = $separator;
    }

    public function getCountElements()
    {
        return count($this->data);
    }

    /**
     * Добавляет элемент хлебных крошек title
     *
     * @param mixed
     * @return void
     */
    public function add()
    {
        foreach (func_get_args() as $value)
        {
            if (is_object($value) && $value instanceof Cover_Array)
            {
                $value = $value->getData();
            }

            if (is_array($value))
            {
                foreach ($value as $element)
                {
                    if ($element = strip_tags($element))
                    {
                        $this->data[] = $element;
                    }
                }
            }
            else
            {
                if ($value = strip_tags($value))
                {
                    $this->data[] = $value;
                }
            }
        }

        return $this;
    }

    /**
     * Удаляет элемент хлебных крошек title
     * под индексом $i.
     *
     * @param int
     * @return void
     */
    public function deleteByIndex($i)
    {
        unset($this->data[$i]);
    }

    /**
     * Возвращает элемент хлебных крошек title
     * под индексом $i.
     *
     * @param int
     * @return string
     */
    public function getByIndex($i)
    {
        return isset($this->data[$i]) ? $this->data[$i] : NULL;
    }

    /**
     * Возвращает строку для подстановки в тег html title
     *
     * @param string
     * @return void
     */
    public function getTitle()
    {
        return htmlspecialchars(implode($this->separator, array_reverse($this->data)), ENT_QUOTES);
    }

    /**
     * Возвращает html-код элемента управления.
     *
     * @param void
     * @return string
     */
    public function getHtml()
    {
        return '<title>'.$this->getTitle().'</title>';
    }
}
?>