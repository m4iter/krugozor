<?php
/**
 * �����-������ ��� ��������� ��������� ����
 * � ����� ��������� ������ ���������.
 */
class Helper_Form
{
    private static $instance;

    /**
     * ���� � ������� ������ ������ ��� ��������� ����������
     * ������������� ����� �����.
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
     * ������������� ������ ��� HTML-���� ������ ������.
     *
     * @access public
     * @param string $template ���� � �������
     * @return void
     */
    public function setFieldErrorTemplate($template)
    {
        if (!file_exists($template))
        {
            throw new Exception('�� ������ ������ ������ ������ ��������� �� ������: '.$template);
        }

        $this->input_error_template = $template;
    }

	/*********************************************************************************
	*   ���������� ����� ����.
	**********************************************************************************/

    /**
     * ���������� ������ Html_ElementInput ���� checkbox.
     *
     * @access public
     * @param string $name ��� ��������
     * @param string|int $value ��������
     * @param string|int $checked_value �������� ��������� - ���� $value � $checked_value �����,
        �� �� checkbox is checked
     * @param array �������������� �������������� ���������
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
     * ���������� ������ Html_ElementInput ���� radio.
     *
     * @access public
     * @param string $name ��� ��������
     * @param string|int $value ��������
     * @param string|int $checked_value �������� ��������� - ���� $value � $checked_value �����,
       �� �� radio is checked
     * @param array �������������� �������������� ���������
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
     * ���������� ��� html-��������: hidden ���� � checkbox.
     * ���������� ����� ��������� ���� ��������������� ��������� ����������.
     *
     * @access public
     * @param string $name ��� �������� hidden � checkbox
     * @param string|int $value �������� checkbox
     * @param string|int $hidden_value �������� hidden
     * @param string|int $checked_value �������� ��������� - ���� $value � $checked_value �����,
       �� checkbox is checked.
     * @param array �������������� �������������� ���������
     * @return string
     */
    public static function inputFullCheckbox($name, $value, $hidden_value=null, $checked_value=null, $params=array())
    {
        $checkbox = self::inputCheckbox($name, $value, $checked_value, $params);
        $hidden = self::inputHidden($name, $hidden_value);

        return $hidden->getHtml().$checkbox->gethtml();
    }

    /**
     * ���������� ������ Html_ElementInput ���� text.
     *
     * @access public
     * @param string $name ��� ��������
     * @param string|int $value ��������
     * @param array �������������� �������������� ���������
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
     * ���������� ������ Html_ElementTextarea.
     *
     * @access public
     * @param string $name ��� ��������
     * @param string|int $value ��������
     * @param array �������������� �������������� ���������
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
     * ���������� ������ Html_ElementInput ���� password.
     *
     * @access public
     * @param string $name ��� ��������
     * @param string|int $value ��������
     * @param array �������������� �������������� ���������
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
     * ���������� ������ Html_ElementInput ���� hidden.
     *
     * @access public
     * @param string $name ��� ��������
     * @param string|int $value ��������
     * @param array �������������� �������������� ���������
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
     * ���������� ������ Html_ElementInput ���� submit.
     *
     * @access public
     * @param string $name ��� ��������
     * @param string|int $value ��������
     * @param array �������������� �������������� ���������
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
     * ���������� ������ Html_ElementInput ���� button.
     *
     * @access public
     * @param string $name ��� ��������
     * @param string|int $value ��������
     * @param array �������������� �������������� ���������
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
     * ���������� ������ Html_ElementInput ���� file.
     *
     * @access public
     * @param string $name ��� ��������
     * @param array �������������� �������������� ���������
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
     * ���������� ������ Html_ElementLabel.
     *
     * @access public
     * @param string $text ����� �����
     * @param string $for ������ �� ID
     * @param array �������������� �������������� ���������
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
     * ���������� ������ Html_ElementOption.
     *
     * @access public
     * @param string $value �������� value ���� option
     * @param string $text ��������� ����-�������� ���� option
     * @param array �������������� �������������� ���������
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
     * ���������� ������ Html_ElementOptgroup.
     *
     * @access public
     * @param string $label �������� �������� label
     * @param array �������������� �������������� ���������
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
     * ���������� ������ Html_ElementSelect.
     *
     * @access public
     * @param string $name ��� ��������
     * @param string|int $checked_value �������� ��������� - ���� $value � $checked_value �����,
       �� checkbox is checked.
     * @param array �������������� �������������� ���������
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
     * ���������� ������ Html_ElementSelect ����������� options
     * �������� �������� ���� � �������� ��������� $int_start - $int_stop.
     *
     * @access public
     * @param string $name ��� ��������
     * @param int $int_start ��������� ��������
     * @param int $int_stop �������� ��������
     * @param string|int $checked_value �������� ��������� - ���� $value � $checked_value �����,
       �� checkbox is checked.
     * @param array �������������� �������������� ���������
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
     * ���������� ������ Html_ElementSelect ����������� options
     * �������� �������� ���� � �������� ���������, ������������ ����������� ��� �� $start � �� $stop.
     * ���� �������� �������� ���� �� �������, �� ������������ select � ������� ������
     * ��� ������ now-15 � ������� ������ ���������� ������� ������ now-80.
     *
     * @access public
     * @param string $name ��� ��������
     * @param string|int $checked_value �������� ��������� - ���� $value � $checked_value �����,
       �� checkbox is checked.
     * @param int $start ��������� ��������
     * @param int $stop �������� ��������
     * @param array �������������� �������������� ���������
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
	*   ���������� ������ ����� ����.
	**********************************************************************************/

    /**
     * ��������� array ��� Cover_Array ���������� �������� ������,
     * ��������� � ���������� ��������� ����� ���� � ����������
     * ������ ������ � ���� HTML-����.
     * HTML-��� ������ �� ������� $this->input_error_template.
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
     * ��������� ������� ��� ����������� ��������� �������� ���� array ��� Cover_Array
     * � ���������� ������ � ������� ������ � �������������� �������� ����� <br />.
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
            // todo: ��������, ����� �� ��� �������������� � ����������.
            // � ���� ��, ��������� ��� �������� ��� ����.
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
     * �����������.
     *
     * @access private
     * @param void
     * @return void
     */
    private function __construct() {}
}
?>