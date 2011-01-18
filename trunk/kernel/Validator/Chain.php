<?php
/**
 * �������� ����� ����������.
 *
 * ������� �������� ������� ����������:
 * ��������� �������������� � ������� �����������
 * <pre>
 * $validator = new Validator_Chain('module_name/lang_file' [, 'module_name/other_lang_file']);
 * </pre>
 * ��� 'module1/lang_file' � 'module2/other_lang_file' �������� ������,
 * ������������ �� ��������� ����� �������� ������ ���������,
 * ������� �������� � Modules/module_name/i18n/validator/lang_file
 * ��������� ����� ����� ��� ������� PHP-������, ������������ � ������� ����������� return
 * ������� ���� '����_������' => '�������� ������', ��������:
 * <pre>
 * <?php
 * return array (
 *     'INCORRECT_AUTH_DATA' => '������������ ������ �����������',
 * )?>
 * </pre>
 * �����, ��������� ����� ����� ����� ��� XML ������. (�������� ��� XML)
 *
 * ����� ����, ��� ��������� �������������, � ������� ������ add �����������
 * ���������� ��������� �������:
 * <pre>
 * $validator->add('key', new Module_Common_Validator_VarEmpty($var));
 * </pre>
 * ��� 'key' - ����, ��� ������� ����� ������������ ������ ������.
 * Module_Common_Validator_VarEmpty - ���������� ���������
 * $var - ����������� ��������
 * ����� ���� ��������� ���� ����������� �������������� � ������� ������ validate().
 * ����� getErrors() ���������� ������ ������ ����
 * key => array('����� ������' [, '����� ������' ...])
 *
 * ����� ����, ��������� � ������� $this->addModelErrors
 * ����� ��������� ������ �� ������, ������� �������� ������������
 * ��������� ����
 *
 * [url] => Array (
 *      [0] => Array (
 *              [0] => INVALID_INT_RANGE
 *              [1] => Array([min] => 10000
 *                           [max] => 2147483647
 *                          )
 *          )
 * )
 */
final class Validator_Chain
{
    /**
     * ������ �����������
     *
     * @access private
     * @var array
     */
    private $list = array();

    /**
     * ������, ������������� � ������������
     * ���������� ������ �������� ������.
     *
     * @access private
     * @var array
     */
    private $i18n_error_messages = array();

    /**
     * ��������� ������ ��� �������� ���������� � ����������
     * ������� �� �����������.
     *
     * @access private
     * @var unknown_type
     */
    private $err;

    /**
     * ���������� ���������� ������ �������� ������.
     *
     * @access private
     * @var array
     */
    private static $i18n_files_extensions = array('php', 'xml');

    /**
     * ��������� �������������� ���������� ���������� - �����
     * ������� �������� ������, ������������ �� ���������
     * ����� �������� ������ ���������. ����������� � ����������
     * $this->i18n_error_messages ���������� ������ ������.
     *
     * @param string
     * @return void
     * @todo: ������� ������ ��� ����������� ���� � ���������� � ������� ������
     */
    public function __construct()
    {
        $args = func_get_args();
        $error_message_files = array();

        foreach ($args as $arg)
        {
            list($module, $file) = explode('/', $arg);

            foreach (self::$i18n_files_extensions as $ext)
            {
                $path = implode(DIRECTORY_SEPARATOR,
                                array(dirname(__DIR__),
                                      'Module',
                                      ucfirst($module),
                                      'i18n',
                                      Base_Registry::getInstance()->config->lang,
                                      'validator',
                                      $file
                                     )
                               ).'.'.$ext;

                if (file_exists($path))
                {
                    $error_message_files[$arg] = array($ext, $path);
                    break;
                }
            }

            if (!isset($error_message_files[$arg]))
            {
                trigger_error('�� ������ ��������� �������� ���� ���������� '.$file.
                              ' ��� ������ '.$module.' ��������� �� ������ '.$arg);
            }
        }

        foreach ($error_message_files as $key => $error_file_data)
        {
            $messages = null;

            switch ($error_file_data[0])
            {
                case 'php':
                    $messages = $this->getPhpFileData($error_file_data[1]);
                    break;
                case 'xml':
                    $messages = $this->getXmlFileData($error_file_data[1]);
                    break;
                // default:
            }

            if ($messages)
            {
                $this->i18n_error_messages = array_merge_recursive($this->i18n_error_messages, $messages);
            }
        }
    }

    /**
     * ��������� ��������� $rule ��� ������ $key � �������� �����������.
     *
     * @access public
     * @param string $key ���� ����������, ��������������� ����� ������������ ����
     * @param object $rule ���������� ���������
     * @return void
     */
    public function add($key, $rule)
    {
        if (isset($this->list[$key]) && is_array($this->list[$key]))
        {
            $this->list[$key][] = $rule;
        }
        else
        {
            $this->list[$key] = array($rule);
        }
    }

    /**
    * �������� �� ���� �����������, ����������� � ������ �����,
    * ��������� ��������� ��������� ������� �� ���.
    * ���� ��������� �� �������� ���������, �.�. ���� ������,
    * ����� �������� � ������ $this->err ����� ���� ���� => ��������,
    * ��� ���� - ���� ����������, � �������� - ����� ���������� �� �������.
    *
    * @access public
    * @param void
    * @return void
    */
    public function validate()
    {
        foreach ($this->list as $key => $rules)
        {
            foreach ($rules as $rule)
            {
                if (!$rule->validate())
                {
                    $this->err[$key][] = $rule->getError();

                    // �� ���������� ������ ������ ����� ��������
                    // �� ������� ������.
                    if ($rule->getBreak())
                    {
                        break;
                    }
                }
            }
        }
    }

    /**
     * ��������� ������ (��� ������) � ��������,
     * ����������� ��� ������� �����������.
     *
     * @access public
     * @param string $user_key ���� ������������� ��������
     * @param string $ERROR_KEY ���� ������ �� ������ �������� ������
     * @param array $placeholders ������ �����-������������ ���� ('placeholder' => '��������')
     * @return void
     */
    public function addError($user_key, $ERROR_KEY, $placeholders=array())
    {
        $this->err[$user_key][] = array($ERROR_KEY, $placeholders);
    }

    /**
     * ��������� ������, ������������ �������.
     *
     * @access public
     * @param array $errors
     * @return void
     */
    public function addModelErrors(array $errors=array())
    {
        foreach ($errors as $key => $data)
        {
            foreach($data as $params)
            {
                $this->addError($key, $params[0], $params[1]);
            }
        }
    }

    /**
     * ���������� �������� ������ ������.
     *
     * @access public
     * @param void
     * @return array
     */
    public function getErrors()
    {
        $output = array();

        if ($this->err)
        {
            foreach ($this->err as $key => $value)
            {
                $output[$key] = $this->makeErrorMessage($value);
            }
        }

        return $output;
    }

    /**
     * �������� ������ �������� ������ �� ����������
     * ����� PHP.
     *
     * @access private
     * @param string $file ���� �� ����� PHP
     * @return array
     */
    private function getPhpFileData($file)
    {
        return (array)include $file;
    }

    /**
     * �������� ������ �������� ������ �� ����������
     * ����� XML.
     *
     * @access private
     * @param string $file ���� �� ����� XML
     * @return array
     */
    private function getXmlFileData($file)
    {
        $data = array();

        $xml = simplexml_load_file($file);

        foreach ($xml->children() as $val)
        {
            $data[strtoupper($val->getName())] = trim((string)$val);
        }

        return $data;
    }

    /**
    * ��������� � ����� ��������� �� ������ $error_message
    * � ������ ������� $error_key.
    * ������ ����� ����� ��� ������� ������� ���������������
    * ������������ ��������� �� �������.
    *
    * @param string $error_key ��� �����
    * @param string $error_message ��������� �� ������
    * @return void

    public function setErrorMessage($error_key, $error_message)
    {
        $this->i18n_error_messages[$error_key] = $error_message;
    }*/

    /**
     * ��������� �������, �������������� ������������ � ���������� �������
     * � ����������������� ����������� �� �������.
     *
     * @access private
     * @param array
     * @return array
     */
    private function makeErrorMessage($in)
    {
        $out = array();

        // key - �������� ��
        // values - ���� ������[0] � ���������[1]
        foreach ($in as $id => $params)
        {
            if (is_array($params[0]))
            {
                $out[$id] = $this->makeErrorMessage($in[$id]);
            }
            else
            {
                $error_message = $this->i18n_error_messages[$params[0]];

                foreach ($params[1] as $k => $v)
                {
                    $error_message = str_replace('{'.$k.'}', htmlspecialchars($v, 3), $error_message);
                }

                $out[$id] = $error_message;
            }
        }

        return $out;
    }
}
?>