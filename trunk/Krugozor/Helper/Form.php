<?php
/**
 * Класс-хэлпер для генерации элементов форм
 * и полей выводящих ошибки валидации.
 */
class Helper_Form
{
    private static $instance;

    /**
     * Путь к шаблону вывода ошибки при ошибочном заполнении
     * пользователем полей ввода.
     *
     * @access private
     * @var string
     */
    private $input_error_template;

    public static function getInstance()
    {
        if (!self::$instance)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Устанавливает шаблон для HTML-кода вывода ошибок.
     *
     * @access public
     * @param string $template путь к шаблону
     * @return void
     */
    public function setFieldErrorTemplate($template)
    {
        if (!file_exists($template))
        {
            throw new Exception('Не найден шаблон вывода ошибок указанный по адресу: '.$template);
        }

        $this->input_error_template = $template;
    }

	/*********************************************************************************
	*   Генераторы полей форм.
	**********************************************************************************/

    /**
     * Возвращает объект Html_ElementInput типа checkbox.
     *
     * @access public
     * @param string $name имя элемента
     * @param string|int $value значение
     * @param string|int $checked_value значение сравнения - если $value и $checked_value равны,
        то то checkbox is checked
     * @param array дополнительные необязательные параметры
     * @return object
     */
    public static function inputCheckbox($name, $value, $checked_value=null, $params=array())
    {
        $object = new Html_ElementInput('checkbox');
        $object->name = $name;
        $object->value = $value;
        $object->setCheckedValue($checked_value);
        $object->setData($params);

        return $object;
    }

    /**
     * Возвращает объект Html_ElementInput типа radio.
     *
     * @access public
     * @param string $name имя элемента
     * @param string|int $value значение
     * @param string|int $checked_value значение сравнения - если $value и $checked_value равны,
       то то radio is checked
     * @param array дополнительные необязательные параметры
     * @return object
     */
    public static function inputRadio($name, $value, $checked_value=null, $params=array())
    {
        $object = new Html_ElementInput('radio');
        $object->name = $name;
        $object->value = $value;
        $object->setCheckedValue($checked_value);
        $object->setData($params);

        return $object;
    }

    /**
     * Возвращает два html-элемента: hidden поле и checkbox.
     * Обобщённый метод получения двух взаимосвязанных элементов управления.
     *
     * @access public
     * @param string $name имя элемента hidden и checkbox
     * @param string|int $value значение checkbox
     * @param string|int $hidden_value значение hidden
     * @param string|int $checked_value значение сравнения - если $value и $checked_value равны,
       то checkbox is checked.
     * @param array дополнительные необязательные параметры
     * @return string
     */
    public static function inputFullCheckbox($name, $value, $hidden_value=null, $checked_value=null, $params=array())
    {
        $checkbox = self::inputCheckbox($name, $value, $checked_value, $params);
        $hidden = self::inputHidden($name, $hidden_value);

        return $hidden->getHtml().$checkbox->gethtml();
    }

    /**
     * Возвращает объект Html_ElementInput типа text.
     *
     * @access public
     * @param string $name имя элемента
     * @param string|int $value значение
     * @param array дополнительные необязательные параметры
     * @return object
     */
    public static function inputText($name, $value, $params=array())
    {
        $object = new Html_ElementInput('text');
        $object->name = $name;
        $object->value = $value;
        $object->setData($params);

        return $object;
    }

    /**
     * Возвращает объект Html_ElementTextarea.
     *
     * @access public
     * @param string $name имя элемента
     * @param string|int $value значение
     * @param array дополнительные необязательные параметры
     * @return object
     */
    public static function inputTextarea($name, $text, $params=array())
    {
        $object = new Html_ElementTextarea();
        $object->name = $name;
        $object->setText($text);
        $object->setData($params);

        return $object;
    }

    /**
     * Возвращает объект Html_ElementInput типа password.
     *
     * @access public
     * @param string $name имя элемента
     * @param string|int $value значение
     * @param array дополнительные необязательные параметры
     * @return object
     */
    public static function inputPassword($name, $value, $params=array())
    {
        $object = new Html_ElementInput('password');
        $object->name = $name;
        $object->value = $value;
        $object->setData($params);

        return $object;
    }

    /**
     * Возвращает объект Html_ElementInput типа hidden.
     *
     * @access public
     * @param string $name имя элемента
     * @param string|int $value значение
     * @param array дополнительные необязательные параметры
     * @return object
     */
    public static function inputHidden($name, $value, $params=array())
    {
        $object = new Html_ElementInput('hidden');
        $object->name = $name;
        $object->value = $value;
        $object->setData($params);

        return $object;
    }

    /**
     * Возвращает объект Html_ElementInput типа submit.
     *
     * @access public
     * @param string $name имя элемента
     * @param string|int $value значение
     * @param array дополнительные необязательные параметры
     * @return object
     */
    public static function inputSubmit($name, $value, $params=array())
    {
        $object = new Html_ElementInput('submit');
        $object->name = $name;
        $object->value = $value;
        $object->setData($params);

        return $object;
    }

    /**
     * Возвращает объект Html_ElementInput типа button.
     *
     * @access public
     * @param string $name имя элемента
     * @param string|int $value значение
     * @param array дополнительные необязательные параметры
     * @return object
     */
    public static function inputButton($name, $value, $params=array())
    {
        $object = new Html_ElementInput('button');
        $object->name = $name;
        $object->value = $value;
        $object->setData($params);

        return $object;
    }

    /**
     * Возвращает объект Html_ElementInput типа file.
     *
     * @access public
     * @param string $name имя элемента
     * @param array дополнительные необязательные параметры
     * @return object
     */
    public static function inputFile($name, $params=array())
    {
        $object = new Html_ElementInput('file');
        $object->name = $name;
        $object->setData($params);

        return $object;
    }

    /**
     * Возвращает объект Html_ElementLabel.
     *
     * @access public
     * @param string $text текст метки
     * @param string $for ссылка на ID
     * @param array дополнительные необязательные параметры
     * @return object
     */
    public static function label($text, $for, $params=array())
    {
        $object = new Html_ElementLabel();
        $object->for = $for;
        $object->setText($text);
        $object->setData($params);

        return $object;
    }

    /**
     * Возвращает объект Html_ElementOption.
     *
     * @access public
     * @param string $value значение value тега option
     * @param string $text текстовой узел-значение тега option
     * @param array дополнительные необязательные параметры
     * @return object
     */
    public static function inputOption($value, $text=null, $params=array())
    {
        $object = new Html_ElementOption();
        $object->value = $value;
        $object->setText($text);
        $object->setData($params);

        return $object;
    }

    /**
     * Возвращает объект Html_ElementOptgroup.
     *
     * @access public
     * @param string $label значение свойства label
     * @param array дополнительные необязательные параметры
     * @return object
     */
    public static function inputOptgroup($label=null, $params=array())
    {
        $object = new Html_ElementOptgroup();
        $object->label = $label;
        $object->setData($params);

        return $object;
    }

    /**
     * Возвращает объект Html_ElementSelect.
     *
     * @access public
     * @param string $name имя элемента
     * @param string|int $checked_value значение сравнения - если $value и $checked_value равны,
       то checkbox is checked.
     * @param array дополнительные необязательные параметры
     * @return object
     */
    public static function inputSelect($name, $checked_value=null, $params=array())
    {
        $object = new Html_ElementSelect();
        $object->name = $name;
        $object->setCheckedValue($checked_value);
        $object->setData($params);

        return $object;
    }

    /**
     * Возвращает объект Html_ElementSelect наполненный options
     * значения которого идут в цифровом диапазоне $int_start - $int_stop.
     *
     * @access public
     * @param string $name имя элемента
     * @param int $int_start начальное значение
     * @param int $int_stop конечное значение
     * @param string|int $checked_value значение сравнения - если $value и $checked_value равны,
       то checkbox is checked.
     * @param array дополнительные необязательные параметры
     * @return object
     */
    public static function inputSelectIntegerValues($name, $int_start, $int_stop, $checked_value=null, $params=array())
    {
        $int_start = (int)$int_start;
        $int_stop = (int)$int_stop;

        $object = new Html_ElementSelect();
        $object->name = $name;
        $object->setCheckedValue($checked_value);
        $object->setData($params);

        $option = new Html_ElementOption();
        $option->value = 0;
        $option->setText('');
        $object->addOption($option);

        if ($int_start < $int_stop)
        {
            for (; $int_start <= $int_stop; $int_start++)
            {
                $option = new Html_ElementOption();
                $option->value = $int_start;
                $option->setText($int_start);

                $object->addOption($option);
            }
        }
        else
        {
            for (; $int_start >= $int_stop; $int_start--)
            {
                $option = new Html_ElementOption();
                $option->value = $int_start;
                $option->setText($int_start);

                $object->addOption($option);
            }
        }

        return $object;
    }

    /**
     * Возвращает объект Html_ElementSelect наполненный options
     * значения которого идут в цифровом диапазоне, определяемом количеством лет со $start и до $stop.
     * Если цифровые значения явно не указаны, то возвращается select с верхней точкой
     * лет равной now-15 и крайней точкой временного отсчёта равной now-80.
     *
     * @access public
     * @param string $name имя элемента
     * @param string|int $checked_value значение сравнения - если $value и $checked_value равны,
       то checkbox is checked.
     * @param int $start начальное значение
     * @param int $stop конечное значение
     * @param array дополнительные необязательные параметры
     * @return object
     */
    public static function inputSelectYears($name, $checked_value, $start=15, $end=80, $params=array())
    {
        $start = date('Y', time()-60*60*24*360*$start);
        $end = date('Y', time()-60*60*24*360*$end);

        $object = new Html_ElementSelect();
        $object->name = $name;
        $object->setCheckedValue($checked_value);
        $object->setData($params);

        $option = new Html_ElementOption();
        $option->value = 0;
        $option->setText('');
        $object->addOption($option);

        while ($start >= $end)
        {
            $option = new Html_ElementOption();
            $option->value = $start;
            $option->setText($start);

            $object->addOption($option);
            $start--;
        }

        return $object;
    }

	/*********************************************************************************
	*   Генераторы ошибок полей форм.
	**********************************************************************************/

    /**
     * Принимает array или Cover_Array содержащий перечень ошибок,
     * возникших в результате валидации полей форм и возвращает
     * строку ошибки в виде HTML-кода.
     * HTML-код берётся из шаблона $this->input_error_template.
     *
     * @access public
     * @param array|Cover_Array
     * @return string
     */
    public function getFieldError($mess=null)
    {
        if ($error_string = self::makeErrorMessage($mess))
        {
            $str_template = file_get_contents($this->input_error_template);

            return str_replace('{error_message}', $error_string, $str_template);
        }

        return $error_string;
    }

    /**
     * Принимает простые или многомерные структуры массивов типа array или Cover_Array
     * с описаниями ошибок и создает строку с перечисленными ошибками через <br />.
     *
     * @access private
     * @param array|Cover_Array
     * @return string
     */
    private static function makeErrorMessage($in)
    {
        if (!count($in))
        {
            return '';
        }

        $buf = '';

        foreach ($in as $id => $value)
        {
            // todo: выяснить, будет ли это использоваться в дальнейшем.
            // и если да, заставить это работать как надо.
            if (is_array($value))
            {
                $buf .= self::makeErrorMessage($value);
            }
            else
            {
                $buf .= $value.'<br class="clear" />';
            }
        }

        return $buf;
    }

    /**
     * Конструктор.
     *
     * @access private
     * @param void
     * @return void
     */
    private function __construct() {}
}
?>